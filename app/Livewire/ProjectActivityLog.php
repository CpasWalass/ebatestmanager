<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use Spatie\Activitylog\Models\Activity;

class ProjectActivityLog extends Component
{
    use WithPagination;

    public $projectId;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
    }

    public function render()
    {
        // On récupère l'activité liée au projet, à ses cas de tests, ou ses exécutions
        // Simplification pour l'exemple : tout ce qui a un subject_type et subject_id lié
        
        $activities = Activity::with('causer')
            ->where(function ($query) {
                // Activités directement sur le projet
                $query->where(function($q) {
                    $q->where('subject_type', Project::class)
                      ->where('subject_id', $this->projectId);
                })
                // Activités sur les cas de test de ce projet
                ->orWhere(function($q) {
                    $q->where('subject_type', \App\Models\TestCase::class)
                      ->whereIn('subject_id', function($sub) {
                          $sub->select('id')
                              ->from('test_cases')
                              ->where('project_id', $this->projectId);
                      });
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.project-activity-log', [
            'activities' => $activities
        ]);
    }
}
