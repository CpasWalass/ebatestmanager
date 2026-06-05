<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'type',
        'tested_version',
        'test_date',
        'responsible',
        'perimeter',
        'stats',
        'notes',
        'sections',
        'status',
        'tenant_id',
    ];

    protected $casts = [
        'stats'    => 'array',
        'sections' => 'array',
        'test_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ReportResponse::class);
    }

    /**
     * Génère les statistiques depuis les TestExecutions du projet
     */
    public function generateStats(): array
    {
        $executions = $this->project->testCases()
            ->with('executions')
            ->get()
            ->flatMap(fn($tc) => $tc->executions);

        $total   = $executions->count();
        $success = $executions->where('status', 'valide')->count();
        $failure = $executions->where('status', 'non_valide')->count();
        $reserve = $executions->where('status', 'sous_reserve')->count();
        $optim   = $executions->where('status', 'optimisation')->count();

        return [
            'total'       => $total,
            'success'     => $success,
            'failure'     => $failure,
            'reserve'     => $reserve,
            'optimisation'=> $optim,
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'        => 'Brouillon',
            'sent'         => 'Envoyé',
            'acknowledged' => 'Pris en compte',
            default        => $this->status,
        };
    }
}
