<?php

namespace App\Livewire;

use App\Imports\TestCaseExcelImport;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class ExcelTestEditor extends Component
{
    use WithFileUploads;

    public Project $project;
    public TestCaseTemplate $template;

    public bool $showColumnModal = false;
    public string $newColumnName = '';
    public string $newColumnType = 'text';

    // Options editor
    public ?string $editingOptionsColumn = null;
    public array $editingOptions = [];
    public bool $showOptionsEditor = false;

    public $excelFile = null;
    public bool $showImportModal = false;
    public ?string $importResult = null;
    public ?string $importError = null;

    // Commit Session
    public bool $showCommitModal = false;
    public string $commitMessage = '';

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->project  = $project;
        $this->template = $template;
    }

    public function openCommitModal(): void
    {
        $this->commitMessage = '';
        $this->showCommitModal = true;
    }

    public function commitSession(): void
    {
        $this->validate([
            'commitMessage' => 'required|min:3|max:500',
        ]);

        activity()
            ->performedOn($this->project)
            ->causedBy(auth()->user())
            ->log("Session de test soumise ({$this->template->name}) : " . $this->commitMessage);

        $this->showCommitModal = false;
        $this->commitMessage = '';
        session()->flash('success', 'Votre session de tests a été validée avec succès.');
    }

    #[Computed]
    public function rows()
    {
        return TestCase::where('template_id', $this->template->id)
            ->orderBy('id')
            ->get();
    }

    public function addRow(): void
    {
        $data = [];
        foreach ($this->template->fields as $field) {
            $data[$field['name']] = '';
        }

        TestCase::create([
            'project_id'  => $this->project->id,
            'template_id' => $this->template->id,
            'data'        => $data,
        ]);
    }

    public function updateCell(int $id, string $field, string $value): void
    {
        $testCase = TestCase::find($id);
        if ($testCase) {
            $data          = $testCase->data ?? [];
            $data[$field]  = $value;
            $testCase->data = $data;
            $testCase->save();
        }
    }

    public function deleteRow(int $id): void
    {
        TestCase::destroy($id);
    }

    public function addColumn(): void
    {
        $this->validate([
            'newColumnName' => 'required|string|min:2|max:50',
            'newColumnType' => 'required|in:text,textarea,select,url',
        ]);

        $fields = $this->template->fields;
        $machineName = strtolower(str_replace(' ', '_', $this->newColumnName));
        
        // Add default options for 'select' type if needed
        $options = [];
        if ($this->newColumnType === 'select') {
            $options = ['Option 1', 'Option 2']; // Default options, could be customized later
        }

        $newField = [
            'name' => $machineName,
            'label' => mb_strtoupper($this->newColumnName),
            'type' => $this->newColumnType,
            'required' => false,
        ];
        
        if (!empty($options)) {
            $newField['options'] = $options;
        }

        $fields[] = $newField;

        $this->template->update(['fields' => $fields]);
        $this->newColumnName = '';
        $this->newColumnType = 'text';
        $this->showColumnModal = false;
        
        // Refresh template
        $this->template->refresh();
    }

    public function removeColumn(string $columnName): void
    {
        $fields = $this->template->fields;
        $fields = array_filter($fields, fn($field) => $field['name'] !== $columnName);
        
        $this->template->update(['fields' => array_values($fields)]);
        $this->template->refresh();
    }

    public function updateColumnType(string $columnName, string $newType): void
    {
        $fields = $this->template->fields;
        
        foreach ($fields as &$field) {
            if ($field['name'] === $columnName) {
                $field['type'] = $newType;
                if ($newType === 'select' && empty($field['options'])) {
                    $field['options'] = ['Option 1', 'Option 2'];
                }
                break;
            }
        }
        
        $this->template->update(['fields' => $fields]);
        $this->template->refresh();
    }

    public function openOptionsEditor(string $columnName): void
    {
        $this->showColumnModal = false; // Fermer le modal colonnes en premier
        $this->editingOptionsColumn = $columnName;
        $this->editingOptions = [];
        
        $fields = $this->template->fields;
        foreach ($fields as $field) {
            if ($field['name'] === $columnName) {
                $options = $field['options'] ?? [];
                $colors  = $field['option_colors'] ?? [];
                foreach ($options as $opt) {
                    $this->editingOptions[] = [
                        'value' => $opt,
                        'color' => $colors[$opt] ?? '#6b7280',
                    ];
                }
                break;
            }
        }
        
        if (empty($this->editingOptions)) {
            $this->editingOptions = [
                ['value' => '', 'color' => '#6b7280'],
            ];
        }
        
        $this->showOptionsEditor = true;
    }

    public function addOption(): void
    {
        $this->editingOptions[] = ['value' => '', 'color' => '#6b7280'];
    }

    public function removeOption(int $index): void
    {
        unset($this->editingOptions[$index]);
        $this->editingOptions = array_values($this->editingOptions);
    }

    public function saveOptions(): void
    {
        $fields = $this->template->fields;
        
        foreach ($fields as &$field) {
            if ($field['name'] === $this->editingOptionsColumn) {
                $options = [];
                $colors  = [];
                foreach ($this->editingOptions as $opt) {
                    $val = trim($opt['value']);
                    if ($val !== '') {
                        $options[] = $val;
                        $colors[$val] = $opt['color'];
                    }
                }
                $field['options'] = $options;
                $field['option_colors'] = $colors;
                break;
            }
        }
        
        $this->template->update(['fields' => $fields]);
        $this->template->refresh();
        $this->showOptionsEditor = false;
        $this->editingOptionsColumn = null;
        $this->editingOptions = [];
    }

    public function closeOptionsEditor(): void
    {
        $this->showOptionsEditor = false;
        $this->editingOptionsColumn = null;
        $this->editingOptions = [];
    }

    #[Computed]
    public function allLinks()
    {
        $projectLinks = $this->project->links ?? [];
        $templateLinks = $this->template->links ?? [];
        
        return array_merge($projectLinks, $templateLinks);
    }

    public function importExcel(): void
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excelFile.required' => 'Veuillez sélectionner un fichier.',
            'excelFile.mimes'    => 'Le fichier doit être un fichier Excel (.xlsx, .xls) ou CSV.',
            'excelFile.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $importer = new TestCaseExcelImport($this->template, $this->project->id);
            $count = $importer->import($path);

            $this->importResult = "$count ligne(s) importée(s) avec succès !";
            $this->importError = null;
            $this->excelFile = null;
            $this->template->refresh();
        } catch (\Exception $e) {
            $this->importError = 'Erreur : ' . $e->getMessage();
            $this->importResult = null;
        }
    }

    public function render()
    {
        return view('livewire.excel-test-editor');
    }
}
