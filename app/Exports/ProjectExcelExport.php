<?php

namespace App\Exports;

use App\Models\Project;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectExcelExport
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function export(): string
    {
        $filename = 'export_' . Str::slug($this->project->name) . '_' . date('Ymd_His') . '.xlsx';
        $directory = storage_path('app/public/exports');
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

        $writer = new Writer();
        $writer->openToFile($filePath);

        $templates = $this->project->templates()->with('testCases')->get();

        $sheetIndex = 0;
        foreach ($templates as $template) {
            // Manage sheets
            if ($sheetIndex === 0) {
                $sheet = $writer->getCurrentSheet();
            } else {
                $sheet = $writer->addNewSheetAndMakeItCurrent();
            }
            
            // Excel sheet names cannot exceed 31 chars and cannot contain certain characters
            $safeName = substr(str_replace(['*', ':', '?', '[', ']', '\\', '/'], '_', $template->name), 0, 31);
            $sheet->setName($safeName);

            // Write Headers
            $fields = is_array($template->fields) ? $template->fields : [];
            $headerCells = [];
            foreach ($fields as $field) {
                $headerCells[] = Cell::fromValue($field['name'] ?? $field['id']);
            }
            $writer->addRow(new Row($headerCells));

            // Write Rows
            foreach ($template->testCases as $testCase) {
                $data = is_array($testCase->data) ? $testCase->data : [];
                $rowCells = [];
                
                foreach ($fields as $field) {
                    $fieldId = $field['id'];
                    $val = $data[$fieldId] ?? '';
                    $rowCells[] = Cell::fromValue((string) $val);
                }
                
                $writer->addRow(new Row($rowCells));
            }
            
            $sheetIndex++;
        }
        
        // If no templates, just add an empty sheet to avoid error
        if ($templates->count() === 0) {
            $writer->addRow(Row::fromValues(['Aucun cas de test']));
        }

        $writer->close();

        return 'public/exports/' . $filename;
    }
}
