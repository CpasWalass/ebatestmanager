<?php

namespace App\Services;

use App\Models\TestCaseTemplate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

class GeminiTestCaseGenerator
{
    /**
     * Nombre maximum de caractères envoyés au modèle (évite de dépasser
     * le contexte et de faire exploser le coût sur un très gros document).
     */
    private const MAX_INPUT_CHARS = 60000;

    /**
     * Extrait le texte brut d'un cahier des charges uploadé (PDF, Word, texte).
     */
    public function extractTextFromFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $text = match ($extension) {
            'pdf' => $this->extractFromPdf($file),
            'doc', 'docx' => $this->extractFromWord($file),
            'txt', 'md' => file_get_contents($file->getRealPath()),
            default => throw new RuntimeException("Format de fichier non pris en charge : .{$extension}"),
        };

        $text = trim((string) $text);

        if ($text === '') {
            throw new RuntimeException("Aucun texte n'a pu être extrait de ce fichier.");
        }

        return mb_substr($text, 0, self::MAX_INPUT_CHARS);
    }

    private function extractFromPdf(UploadedFile $file): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($file->getRealPath());

        return $pdf->getText();
    }

    private function extractFromWord(UploadedFile $file): string
    {
        $phpWord = WordIOFactory::load($file->getRealPath());
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $sub) {
                        if (method_exists($sub, 'getText')) {
                            $text .= $sub->getText() . "\n";
                        }
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Envoie le texte source à Gemini et retourne un tableau de cas de test
     * proposés, structurés selon les champs du template ciblé.
     *
     * @return array<int, array<string, mixed>>
     */
    public function generate(string $sourceText, TestCaseTemplate $template): array
    {
        $fields = $template->fields ?? TestCaseTemplate::defaultFields();
        $writableFields = $this->getWritableFields($fields);

        $prompt = $this->buildPrompt($sourceText, $writableFields);

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withOptions(['query' => ['key' => config('services.gemini.key'),],])
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent',
                [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );
            //->withOptions(['query' => ['key' => config('services.gemini.key')]]);

        if ($response->failed()) {
            Log::error('Gemini generation failed', ['body' => $response->body()]);
            throw new RuntimeException("La génération IA a échoué (erreur API Gemini).");
        }

        $raw = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $raw) {
            throw new RuntimeException("Réponse inattendue du modèle IA.");
        }

        $cases = json_decode($raw, true);

        if (! is_array($cases)) {
            throw new RuntimeException("Le modèle n'a pas renvoyé un JSON exploitable.");
        }

        return $this->sanitizeCases($cases, $writableFields);
    }

    /**
     * Détermine les champs que l'IA doit remplir.
     * Gère les templates par défaut et personnalisés.
     */
    private function getWritableFields(array $fields): array
    {
        $hasDefaultFields = collect($fields)->contains('name', 'cas_test');
        
        if ($hasDefaultFields) {
            $allowed = ['cas_test', 'modules', 'fonctionnalites', 'scenarios_test', 'resultats_attendus'];
            return array_values(array_filter($fields, fn ($f) => in_array($f['name'], $allowed, true)));
        }
        
        // Template personnalisé : on demande à l'IA de remplir tous les champs
        // sauf ceux qui ressemblent à du suivi (statut, état, commentaire)
        return array_values(array_filter($fields, function ($f) {
            $name = strtolower($f['name']);
            if (str_contains($name, 'statut') || str_contains($name, 'etat') || str_contains($name, 'avis') || str_contains($name, 'comment')) {
                return false;
            }
            return true;
        }));
    }

    private function buildPrompt(string $sourceText, array $fields): string
    {
        $schema = collect($fields)->map(fn ($f) => sprintf('- "%s" (%s) : %s', $f['name'], $f['type'], $f['label']))->implode("\n");
        $fieldNames = collect($fields)->pluck('name')->implode('", "');

        return <<<PROMPT
Tu es un(e) assistant(e) QA expérimenté(e). À partir du document source ci-dessous
(un cahier des charges ou une description de workflow), propose une liste de cas de test
UAT/IAT pertinents et couvrant les principaux scénarios fonctionnels décrits.

Réponds UNIQUEMENT avec un tableau JSON valide, sans texte autour, où chaque élément
respecte exactement ce schéma de champs :
{$schema}

Utilise strictement les clés suivantes pour chaque objet JSON : "{$fieldNames}".
Ne propose pas plus de 15 cas de test. Sois concis mais précis dans les scénarios et
résultats attendus.

--- DOCUMENT SOURCE ---
{$sourceText}
--- FIN DU DOCUMENT ---
PROMPT;
    }

    /**
     * Ne garde que les champs attendus et force les types en chaîne,
     * pour éviter d'insérer des données inattendues dans test_cases.data.
     */
    private function sanitizeCases(array $cases, array $fields): array
    {
        $allowedKeys = collect($fields)->pluck('name')->all();

        return collect($cases)
            ->filter(fn ($case) => is_array($case))
            ->map(function (array $case) use ($allowedKeys) {
                $clean = [];
                foreach ($allowedKeys as $key) {
                    $clean[$key] = isset($case[$key]) ? trim((string) $case[$key]) : '';
                }
                return $clean;
            })
            ->filter(function ($case) {
                // On garde le cas s'il a au moins un champ non vide
                $nonEmpty = array_filter($case, fn($val) => trim((string)$val) !== '');
                return count($nonEmpty) > 0;
            })
            ->values()
            ->all();
    }
}
