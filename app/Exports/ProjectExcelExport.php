<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\TestCaseTemplate;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ProjectExcelExport
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function export(): string
    {
        $filename = 'export_'.Str::slug($this->project->name).'_'.date('Ymd_His').'.xlsx';
        $directory = storage_path('app/public/exports');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory.DIRECTORY_SEPARATOR.$filename;

        $writer = new Writer;
        $writer->openToFile($filePath);

        $templates = $this->project->templates()->with('testCases')->get();
        $progressLabels = TestCaseTemplate::progressOptions();
        $verdictLabels = TestCaseTemplate::verdictOptions();

        $sheetIndex = 0;
        foreach ($templates as $template) {
            if ($sheetIndex === 0) {
                $sheet = $writer->getCurrentSheet();
            } else {
                $sheet = $writer->addNewSheetAndMakeItCurrent();
            }

            $safeName = substr(str_replace(['*', ':', '?', '[', ']', '\\', '/'], '_', $template->name), 0, 31);
            $sheet->setName($safeName);

            $fields = is_array($template->fields) ? $template->fields : [];

            // ETAT DE TEST / STATUS ne sont plus des champs libres du template
            // (Phase 1) : on les ajoute explicitement en fin de ligne, sans quoi
            // le fichier exporté perdrait ces deux colonnes.
            $headerCells = [];
            foreach ($fields as $field) {
                $headerCells[] = Cell::fromValue($field['name'] ?? $field['id'] ?? '');
            }
            $headerCells[] = Cell::fromValue('ETAT DE TEST');
            $headerCells[] = Cell::fromValue('STATUS');
            $writer->addRow(new Row($headerCells));

            foreach ($template->testCases as $testCase) {
                $data = is_array($testCase->data) ? $testCase->data : [];
                $rowCells = [];

                foreach ($fields as $field) {
                    $key = $field['name'] ?? $field['id'] ?? '';
                    $val = $data[$key] ?? '';
                    $rowCells[] = Cell::fromValue((string) $val);
                }

                $rowCells[] = Cell::fromValue($progressLabels[$testCase->progress]['label'] ?? '');
                $rowCells[] = Cell::fromValue($testCase->verdict ? ($verdictLabels[$testCase->verdict]['label'] ?? '') : '');

                $writer->addRow(new Row($rowCells));
            }

            $sheetIndex++;
        }

        if ($templates->count() === 0) {
            $writer->addRow(Row::fromValues(['Aucun cas de test']));
        }

        $writer->close();

        return 'public/exports/'.$filename;
    }
}
