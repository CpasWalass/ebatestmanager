<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ExcelTestEditor extends Component
{
    public Project $project;
    public TestCaseTemplate $template;

    public bool $showColumnModal = false;
    public string $newColumnName = '';
    public string $newColumnType = 'text';

    public function mount(Project $project, TestCaseTemplate $template): void
    {
        $this->project  = $project;
        $this->template = $template;
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

    #[Computed]
    public function allLinks()
    {
        $projectLinks = $this->project->links ?? [];
        $templateLinks = $this->template->links ?? [];
        
        return array_merge($projectLinks, $templateLinks);
    }

    public function render()
    {
        return view('livewire.excel-test-editor');
    }
}
