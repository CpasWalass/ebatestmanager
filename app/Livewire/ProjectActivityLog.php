<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TestCase;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ProjectActivityLog extends Component
{
    use WithPagination;

    public $projectId;

    public ?Project $project = null;

    public function mount($projectId): void
    {
        $this->project = Project::findOrFail($projectId);
        $this->authorize('view', $this->project);

        $this->projectId = $projectId;
    }

    public function render()
    {
        // Activité liée au projet lui-même, ou à ses cas de test.
        $activities = Activity::with('causer')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('subject_type', Project::class)
                        ->where('subject_id', $this->projectId);
                })
                    ->orWhere(function ($q) {
                        $q->where('subject_type', TestCase::class)
                            ->whereIn('subject_id', function ($sub) {
                                $sub->select('id')
                                    ->from('test_cases')
                                    ->where('project_id', $this->projectId);
                            });
                    });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.project-activity-log', [
            'activities' => $activities,
            'project' => $this->project,
        ]);
    }
}
