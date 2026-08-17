<?php

namespace App\Imports;

use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Même correctif que ProjectExcelImport : les colonnes ETAT DE TEST/STATUS
 * sont détectées à part et routées vers progress/verdict, plutôt que
 * silencieusement ignorées (elles ne correspondent à aucun champ du template
 * depuis que ces deux axes ne sont plus des champs libres — Phase 1).
 */
class TestCaseExcelImport
{
    protected TestCaseTemplate $template;

    protected int $projectId;

    public function __construct(TestCaseTemplate $template, int $projectId)
    {
        $this->template = $template;
        $this->projectId = $projectId;
    }

    protected static function specialColumnLabels(): array
    {
        return [
            'etatdetest' => 'progress',
            'etat' => 'progress',
            'status' => 'verdict',
            'statut' => 'verdict',
        ];
    }

    public function import(string $filePath): int
    {
        $reader = new Reader;
        $reader->open($filePath);

        $fieldMap = [];
        foreach ($this->template->fields as $field) {
            $normalized = TestCaseTemplate::normalizeLabel($field['label']);
            $fieldMap[$normalized] = $field['name'];
        }

        $specialAliases = self::specialColumnLabels();

        $headerRowIndex = null;
        $columnMap = [];
        $specialColumnMap = [];

        $importedCount = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            $rowIndex = 1;
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

                if ($headerRowIndex === null) {
                    $matches = [];
                    $specialMatches = [];

                    foreach ($cells as $colIndex => $cellValue) {
                        if (empty($cellValue)) {
                            continue;
                        }

                        if ($cellValue instanceof \DateTimeInterface) {
                            $cellValue = $cellValue->format('Y-m-d H:i:s');
                        }

                        $normalized = TestCaseTemplate::normalizeLabel((string) $cellValue);

                        if (isset($specialAliases[$normalized])) {
                            $specialMatches[$colIndex] = $specialAliases[$normalized];
                        } elseif (isset($fieldMap[$normalized])) {
                            $matches[$colIndex] = $fieldMap[$normalized];
                        }
                    }

                    if (count($matches) + count($specialMatches) >= 2) {
                        $headerRowIndex = $rowIndex;
                        $columnMap = $matches;
                        $specialColumnMap = $specialMatches;
                    }
                } else {
                    $hasData = false;
                    foreach ($columnMap + $specialColumnMap as $colIndex => $target) {
                        if (! empty($cells[$colIndex] ?? '')) {
                            $hasData = true;
                            break;
                        }
                    }

                    if ($hasData) {
                        $data = [];
                        foreach ($this->template->fields as $field) {
                            $data[$field['name']] = '';
                        }

                        foreach ($columnMap as $colIndex => $fieldName) {
                            $val = $cells[$colIndex] ?? '';
                            if ($val instanceof \DateTimeInterface) {
                                $val = $val->format('Y-m-d H:i:s');
                            }
                            $data[$fieldName] = trim((string) $val);
                        }

                        $progress = 'a_faire';
                        $verdict = null;

                        foreach ($specialColumnMap as $colIndex => $target) {
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
                            'template_id' => $this->template->id,
                            'progress' => $progress,
                            'verdict' => $verdict,
                            'source' => 'excel',
                            'data' => $data,
                        ]);

                        $importedCount++;
                    }
                }

                $rowIndex++;
            }
            break;
        }

        $reader->close();

        if ($headerRowIndex === null) {
            throw new \Exception('Impossible de détecter la ligne d\'en-tête. Vérifiez que les colonnes correspondent aux champs attendus (ex: '.implode(', ', array_keys($fieldMap)).').');
        }

        return $importedCount;
    }
}
