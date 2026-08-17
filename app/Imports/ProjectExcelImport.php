<?php

namespace App\Imports;

use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;

/**
 * IMPORTANT : avant cette réécriture, les colonnes "ETAT DE TEST" et "STATUS"
 * d'un fichier Excel importé étaient traitées comme n'importe quel champ libre
 * et finissaient en texte brut dans data['etat_test']/data['status']. Depuis
 * la Phase 1, ces deux axes sont des colonnes réelles (progress/verdict) sur
 * test_cases, gérées séparément de `data`. Cet import a été mis à jour pour
 * détecter ces deux colonnes spécifiquement et les convertir vers les bonnes
 * valeurs d'enum au lieu de les laisser dans le JSON libre — sinon, un import
 * Excel aurait recréé exactement le bug de KPI qu'on a corrigé.
 */
class ProjectExcelImport
{
    protected int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    protected static function specialColumnSlugs(): array
    {
        return [
            'etat_test' => 'progress',
            'etat' => 'progress',
            'etat_de_test' => 'progress',
            'status' => 'verdict',
            'statut' => 'verdict',
        ];
    }

    protected static function fieldAliases(): array
    {
        return [
            'resultat_obtenu' => 'resultats_obtenus',
            'resultat_attendu' => 'resultats_attendus',
            'scenario_de_test' => 'scenarios_test',
            'scenarios_de_test' => 'scenarios_test',
            'nature_du_test' => 'nature',
            'type_derreur' => 'nature',
            'type_erreur' => 'nature',
            'type_derreurs' => 'nature',
        ];
    }

    /**
     * Champs reconnus comme des SELECT avec des options prédéfinies lors de
     * l'import (ex: NATURE). Permet d'afficher une liste déroulante cohérente
     * avec le vocabulaire de l'application au lieu d'un texte libre.
     *
     * @return array<string, callable>
     */
    protected static function selectFields(): array
    {
        return [
            'nature' => fn () => TestCaseTemplate::natureOptions(),
        ];
    }

    public function import(string $filePath): array
    {
        $reader = new Reader;
        $reader->open($filePath);

        $sheetsImported = 0;
        $rowsImported = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            if (method_exists($sheet, 'isVisible') && ! $sheet->isVisible()) {
                continue;
            }

            $sheetName = $sheet->getName();

            $rows = [];
            $rowCount = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
                $rowCount++;
                if ($rowCount > 10000) {
                    break;
                }
            }

            if (empty($rows)) {
                continue;
            }

            $headerRowIndex = $this->detectHeaderRow($rows);
            if ($headerRowIndex === -1) {
                continue;
            }

            $headerCells = $rows[$headerRowIndex];
            $fields = [];
            $fieldMap = [];
            $specialMap = [];
            $specialAliases = self::specialColumnSlugs();
            $fieldAliases = self::fieldAliases();

            foreach ($headerCells as $colIndex => $cellValue) {
                if ($cellValue instanceof \DateTimeInterface) {
                    $cellValue = $cellValue->format('Y-m-d H:i:s');
                }

                $label = trim((string) $cellValue);
                if (empty($label)) {
                    continue;
                }

                $slug = Str::slug($label, '_');

                if (isset($specialAliases[$slug])) {
                    $specialMap[$colIndex] = $specialAliases[$slug];

                    continue;
                }

                $resolvedSlug = $fieldAliases[$slug] ?? $slug;

                $originalSlug = $resolvedSlug;
                $counter = 1;
                while (collect($fields)->contains('name', $resolvedSlug)) {
                    $resolvedSlug = $originalSlug.'_'.$counter;
                    $counter++;
                }

                $type = 'text';
                if (Str::length($label) > 30 || Str::contains(mb_strtolower($label), ['description', 'resultat', 'scénario', 'scenario', 'attendu', 'obtenu', 'commentaire'])) {
                    $type = 'textarea';
                }

                $fieldDef = [
                    'name' => $resolvedSlug,
                    'label' => $label,
                    'type' => $type,
                    'required' => false,
                ];

                $selectFields = self::selectFields();
                if (isset($selectFields[$resolvedSlug])) {
                    $options = $selectFields[$resolvedSlug]();
                    $fieldDef['type'] = 'select';
                    $fieldDef['options'] = array_values($options);
                    $fieldDef['option_colors'] = array_fill_keys(array_values($options), '#6b7280');
                }

                $fields[] = $fieldDef;

                $fieldMap[$colIndex] = $resolvedSlug;
            }

            if (empty($fields) && empty($specialMap)) {
                continue;
            }

            $template = TestCaseTemplate::create([
                'project_id' => $this->projectId,
                'name' => $sheetName,
                'fields' => $fields ?: TestCaseTemplate::defaultFields(),
                'links' => [],
            ]);

            $sheetsImported++;

            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $cells = $rows[$i];

                $hasData = false;
                foreach ($fieldMap as $colIndex => $slug) {
                    if (! empty($cells[$colIndex])) {
                        $hasData = true;
                        break;
                    }
                }
                foreach ($specialMap as $colIndex => $target) {
                    if (! empty($cells[$colIndex])) {
                        $hasData = true;
                        break;
                    }
                }

                if (! $hasData) {
                    continue;
                }

                $data = [];
                foreach ($fields as $field) {
                    $data[$field['name']] = '';
                }

                foreach ($fieldMap as $colIndex => $slug) {
                    $val = $cells[$colIndex] ?? '';
                    if ($val instanceof \DateTimeInterface) {
                        $val = $val->format('Y-m-d H:i:s');
                    }
                    $val = trim((string) $val);

                    $selectFields = self::selectFields();
                    if (isset($selectFields[$slug]) && $val !== '') {
                        $resolved = TestCaseTemplate::resolveNatureValue($val);
                        if ($resolved !== null) {
                            $val = $resolved;
                        }
                    }

                    $data[$slug] = $val;
                }

                $progress = 'a_faire';
                $verdict = null;

                foreach ($specialMap as $colIndex => $target) {
                    $val = trim((string) ($cells[$colIndex] ?? ''));
                    if ($val === '') {
                        continue;
                    }

                    if ($target === 'progress') {
                        $progress = TestCaseTemplate::resolveProgressValue($val) ?? 'a_faire';
                    } elseif ($target === 'verdict') {
                        $verdict = TestCaseTemplate::resolveVerdictValue($val);
                    }
                }

                TestCase::create([
                    'project_id' => $this->projectId,
                    'template_id' => $template->id,
                    'progress' => $progress,
                    'verdict' => $verdict,
                    'source' => 'excel',
                    'data' => $data,
                ]);

                $rowsImported++;
            }
        }

        $reader->close();

        return [
            'sheets' => $sheetsImported,
            'rows' => $rowsImported,
        ];
    }

    private function detectHeaderRow(array $rows): int
    {
        $bestScore = 0;
        $bestIndex = -1;

        $keywords = ['cas', 'test', 'statut', 'status', 'description', 'resultat', 'attendu', 'obtenu', 'etat', 'priorite', 'scenario', 'etape'];

        $limit = min(20, count($rows));

        for ($i = 0; $i < $limit; $i++) {
            $score = 0;
            $cells = $rows[$i];

            foreach ($cells as $cell) {
                if ($cell instanceof \DateTimeInterface) {
                    continue;
                }

                $val = trim((string) $cell);
                if (! empty($val)) {
                    $score += 1;

                    $normalized = TestCaseTemplate::normalizeLabel($val);

                    foreach ($keywords as $kw) {
                        if (str_contains($normalized, $kw)) {
                            $score += 3;
                            break;
                        }
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIndex = $i;
            }
        }

        return $bestScore >= 2 ? $bestIndex : -1;
    }
}
