<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AssignTesters extends Component
{
    public bool $showModal = false;

    public ?Project $project = null;

    public ?TestCaseTemplate $template = null;

    // Checkboxes array for testers: [userId => boolean]
    public array $selectedTesters = [];

    #[On('openAssignModal')]
    public function openModal($projectId = null, $templateId = null): void
    {
        $this->showModal = true;
        $this->selectedTesters = [];

        if ($projectId) {
            $this->project = Project::find($projectId);
        }

        if ($templateId) {
            $this->template = TestCaseTemplate::find($templateId);
        }

        // Load already assigned testers
        if ($this->template) {
            $assignedIds = TestCaseAssignment::where('template_id', $this->template->id)->pluck('user_id')->toArray();
        } elseif ($this->project) {
            $assignedIds = TestCaseAssignment::where('project_id', $this->project->id)->pluck('user_id')->toArray();
        } else {
            $assignedIds = [];
        }

        foreach ($assignedIds as $id) {
            $this->selectedTesters[$id] = true;
        }
    }

    #[Computed]
    public function testers()
    {
        return User::role('tester')
            ->withCount(['assignments as workload' => function ($query) {
                $query->where('status', 'pending')->orWhere('status', 'in_progress');
            }])
            ->orderBy('workload', 'asc')
            ->get();
    }

    public function save(): void
    {
        $selectedIds = array_keys(array_filter($this->selectedTesters));

        // Ensure project is loaded if we only have template
        if ($this->template && ! $this->project) {
            $this->project = $this->template->project;
        }

        if ($this->template) {
            // Assign to template
            TestCaseAssignment::where('template_id', $this->template->id)->whereNotIn('user_id', $selectedIds)->delete();

            foreach ($selectedIds as $userId) {
                $assignment = TestCaseAssignment::firstOrCreate([
                    'template_id' => $this->template->id,
                    'user_id' => $userId,
                ], [
                    'scope' => 'full_case',
                    'status' => 'pending',
                ]);

                if ($assignment->wasRecentlyCreated && $this->project) {
                    $url = route('testeur.executer', [$this->project->id, $this->template->id]);
                    Message::create([
                        'sender_id' => auth()->id(),
                        'receiver_id' => $userId,
                        'project_id' => $this->project->id,
                        'type' => 'system',
                        'content' => "Vous avez été assigné au cas de test {$this->template->name}. [Cliquez ici pour y accéder]($url)",
                    ]);
                }
            }
        } elseif ($this->project) {
            // Assign to project
            TestCaseAssignment::where('project_id', $this->project->id)->whereNotIn('user_id', $selectedIds)->delete();

            foreach ($selectedIds as $userId) {
                $assignment = TestCaseAssignment::firstOrCreate([
                    'project_id' => $this->project->id,
                    'user_id' => $userId,
                ], [
                    'scope' => 'full_case',
                    'status' => 'pending',
                ]);

                if ($assignment->wasRecentlyCreated) {
                    $url = route('testeur.projets.show', $this->project->id);
                    Message::create([
                        'sender_id' => auth()->id(),
                        'receiver_id' => $userId,
                        'project_id' => $this->project->id,
                        'type' => 'system',
                        'content' => "Vous avez été assigné au projet {$this->project->name}. [Cliquez ici pour y accéder]($url)",
                    ]);
                }
            }
        }

        $this->showModal = false;
        $this->dispatch('assignments-updated');
        session()->flash('success', 'Testeurs assignés avec succès et notifiés.');
    }

    public function render()
    {
        return view('livewire.assign-testers');
    }
}
