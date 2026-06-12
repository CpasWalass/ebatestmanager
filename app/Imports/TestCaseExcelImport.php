<?php

namespace App\Imports;

use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Common\Entity\Row;

class TestCaseExcelImport
{
    protected TestCaseTemplate $template;
    protected int $projectId;

    public function __construct(TestCaseTemplate $template, int $projectId)
    {
        $this->template = $template;
        $this->projectId = $projectId;
    }

    /**
     * Import rows from an uploaded Excel/Xlsx file using OpenSpout.
     * Automatically detects the header row by matching template field labels.
     * Returns number of rows imported.
     */
    public function import(string $filePath): int
    {
        $reader = new Reader();
        $reader->open($filePath);

        $fieldMap = [];
        foreach ($this->template->fields as $field) {
            $normalized = $this->normalize($field['label']);
            $fieldMap[$normalized] = $field['name'];
        }

        $headerRowIndex = null;
        $columnMap = []; // column index => field name

        $importedCount = 0;
        
        foreach ($reader->getSheetIterator() as $sheet) {
            $rowIndex = 1;
            foreach ($sheet->getRowIterator() as $row) {
                // toArray() returns an array of values (strings, DateTimes, ints, etc.)
                $cells = $row->toArray();
                
                // 1. Detect Header Row
                if ($headerRowIndex === null) {
                    $matches = [];
                    foreach ($cells as $colIndex => $cellValue) {
                        if (empty($cellValue)) continue;
                        $normalized = $this->normalize((string) $cellValue);
                        if (isset($fieldMap[$normalized])) {
                            $matches[$colIndex] = $fieldMap[$normalized];
                        }
                    }
                    
                    // If we matched at least 2 field labels, this is the header row
                    if (count($matches) >= 2) {
                        $headerRowIndex = $rowIndex;
                        $columnMap = $matches;
                    }
                } 
                // 2. Read Data Rows
                else {
                    $hasData = false;
                    foreach ($columnMap as $colIndex => $fieldName) {
                        $cellValue = $cells[$colIndex] ?? '';
                        if (!empty($cellValue)) {
                            $hasData = true;
                            break;
                        }
                    }

                    if ($hasData) {
                        // Build data array
                        $data = [];
                        foreach ($this->template->fields as $field) {
                            $data[$field['name']] = '';
                        }

                        foreach ($columnMap as $colIndex => $fieldName) {
                            $val = $cells[$colIndex] ?? '';
                            // Handle date objects from Excel if needed
                            if ($val instanceof \DateTimeInterface) {
                                $val = $val->format('Y-m-d H:i:s');
                            }
                            $data[$fieldName] = trim((string) $val);
                        }

                        TestCase::create([
                            'project_id'  => $this->projectId,
                            'template_id' => $this->template->id,
                            'data'        => $data,
                        ]);

                        $importedCount++;
                    }
                }
                
                $rowIndex++;
            }
            break; // Only read the first sheet
        }

        $reader->close();

        if ($headerRowIndex === null) {
            throw new \Exception('Impossible de détecter la ligne d\'en-tête. Vérifiez que les colonnes correspondent aux champs attendus (ex: ' . implode(', ', array_keys($fieldMap)) . ').');
        }

        return $importedCount;
    }

    private function normalize(string $str): string
    {
        $str = mb_strtolower(trim($str));
        // Simple mapping to handle common french accents without iconv errors
        $str = strtr(
            utf8_decode($str),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
        $str = preg_replace('/[^a-z0-9]/', '', $str); // Remove spaces, punctuation
        return $str;
    }
}
