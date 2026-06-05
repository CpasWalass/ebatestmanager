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

    public function render()
    {
        return view('livewire.excel-test-editor');
    }
}
