<?php

namespace App\Livewire;

use App\Jobs\GenerateTestCasesWithAi;
use App\Models\AiGenerationRequest;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use App\Services\GeminiTestCaseGenerator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AiTestCaseGenerator extends Component
{
    use WithFileUploads;

    public Project $project;

    public TestCaseTemplate $template;

    public bool $showModal = false;

    public string $inputMode = 'text';   // 'file' | 'text'

    public $sourceFile = null;

    public string $workflowText = '';

    public ?int $generationRequestId = null;

    public array $reviewRows = [];

    public string $errorMessage = '';

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
        $this->template = $template;
    }

    /* ------------------------------------------------------------------ */
    /*  Ouverture / fermeture */
    /* ------------------------------------------------------------------ */

    #[On('open-ai-generator')]
    public function openModal(): void
    {
        $this->reset(['reviewRows', 'generationRequestId', 'errorMessage', 'workflowText', 'sourceFile']);
        $this->inputMode = 'text';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['reviewRows', 'generationRequestId', 'errorMessage', 'workflowText', 'sourceFile']);
    }

    public function setInputMode(string $mode): void
    {
        $this->inputMode = $mode;
    }

    /* ------------------------------------------------------------------ */
    /*  Étape 1 — génération (synchrone) */
    /* ------------------------------------------------------------------ */

    public function startGeneration(GeminiTestCaseGenerator $generator): void
    {
        $this->errorMessage = '';
        $this->reviewRows = [];

        if (! auth()->user()->hasRole('chef_project')) {
            $this->errorMessage = 'Seul le Chef Projet peut générer des cas de test par IA.';

            return;
        }

        try {
            if ($this->inputMode === 'file') {
                $this->validate(['sourceFile' => 'required|file|mimes:pdf,doc,docx,txt|max:10240']);
                $extractedText = $generator->extractTextFromFile($this->sourceFile);
                $inputType = 'file';
                $filename = $this->sourceFile->getClientOriginalName();
            } else {
                $this->validate(['workflowText' => 'required|string|min:10|max:20000']);
                $extractedText = $this->workflowText;
                $inputType = 'text';
                $filename = null;
            }
        } catch (ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->first();

            return;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        // Créer l'enregistrement de la demande
        $genRequest = AiGenerationRequest::create([
            'project_id' => $this->project->id,
            'template_id' => $this->template->id,
            'user_id' => auth()->id(),
            'input_type' => $inputType,
            'input_filename' => $filename,
            'extracted_text' => $extractedText,
            'status' => 'pending',
        ]);

        $this->generationRequestId = $genRequest->id;
        $this->sourceFile = null;

        // Exécution synchrone (pas de worker nécessaire)
        GenerateTestCasesWithAi::dispatch($genRequest);

        // Relire le statut depuis la DB après exécution
        $genRequest->refresh();

        if ($genRequest->status === 'failed') {
            $this->errorMessage = $genRequest->error_message ?? 'La génération a échoué.';
            $this->generationRequestId = null;

            return;
        }

        if ($genRequest->status === 'completed' && ! empty($genRequest->proposed_cases)) {
            // Création d'un squelette vide avec tous les champs du template pour éviter les "Undefined array key"
            $defaultFields = [];
            foreach ($this->template->fields ?? TestCaseTemplate::defaultFields() as $f) {
                $defaultFields[$f['name']] = '';
            }

            $this->reviewRows = collect($genRequest->proposed_cases)
                ->map(fn ($c) => array_merge($defaultFields, (array) $c, ['selected' => true]))
                ->values()
                ->all();
        } else {
            $this->errorMessage = 'Aucun cas de test n\'a pu être généré.';
            $this->generationRequestId = null;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Sélection / désélection globale */
    /* ------------------------------------------------------------------ */

    public function toggleSelectAll(bool $value): void
    {
        foreach ($this->reviewRows as $i => $row) {
            $this->reviewRows[$i]['selected'] = $value;
        }
    }

    public function restartGeneration(): void
    {
        $this->reset(['reviewRows', 'generationRequestId', 'errorMessage']);
    }

    /* ------------------------------------------------------------------ */
    /*  Étape 3 — confirmation et persistance */
    /* ------------------------------------------------------------------ */

    public function saveAiCases(): void
    {
        try {
            $selected = collect($this->reviewRows)
                ->filter(fn ($row) => ! empty($row['selected']));

            if ($selected->isEmpty()) {
                $this->errorMessage = 'Veuillez sélectionner au moins un cas de test.';

                return;
            }

            // Noms de tous les champs du template (pour la base vide)
            $templateFields = collect($this->template->fields)->pluck('name')->toArray();

            // Valeurs par défaut pour les champs de suivi non générés par l'IA
            // (etat_test/status ne sont plus des champs libres depuis la Phase 1 —
            // ils vivent dans les colonnes progress/verdict, réglées explicitement
            // ci-dessous plutôt que comme entrées de $data).
            $defaults = [
                'resultats_obtenus' => '',
                'nature' => 'Concluant',
                'commentaires' => '',
            ];

            foreach ($selected as $row) {
                unset($row['selected']);

                $data = array_merge(
                    array_fill_keys($templateFields, ''),
                    $defaults,
                    $row
                );

                TestCase::create([
                    'template_id' => $this->template->id,
                    'project_id' => $this->project->id,
                    'type' => $this->project->type === 'uat' ? 'uat' : 'iat',
                    'progress' => 'a_faire',
                    'verdict' => null,
                    'data' => $data,
                    'source' => 'ai',
                ]);
            }

            $count = $selected->count();

            $this->showModal = false;
            $this->reset(['reviewRows', 'generationRequestId', 'errorMessage', 'workflowText', 'sourceFile']);

            // Notifier l'éditeur de recharger ses lignes
            $this->dispatch('ai-test-cases-added', count: $count);

            session()->flash('success', "{$count} cas de test générés par IA et ajoutés avec succès.");

        } catch (\Throwable $e) {
            $this->errorMessage = "Erreur lors de l'enregistrement : ".$e->getMessage();
        }
    }

    /* ------------------------------------------------------------------ */

    public function render()
    {
        return view('livewire.ai-test-case-generator');
    }
}
