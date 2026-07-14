<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TestCase extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'template_id',
        'project_id',
        'data',
        'tenant_id',
        'type',
        'client_status',
        'client_comment',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function getExecutionStatusAttribute()
    {
        $status = strtolower($this->data['status'] ?? $this->data['etat_test'] ?? '');
        if (in_array($status, ['validé', 'terminé', 'valide', 'termine'])) return 'valide';
        if (in_array($status, ['non validé', 'échec', 'non valide', 'echec'])) return 'non_valide';
        if (in_array($status, ['sous réserve', 'sous reserve'])) return 'sous_reserve';
        if (in_array($status, ['optimisation', 'en cours', 'a faire', 'à faire'])) return 'optimisation';
        
        return null; // Not executed
    }

    public static function calculateStats($cases)
    {
        $stats = [
            'total' => $cases->count(),
            'executed' => 0,
            'valide' => 0,
            'non_valide' => 0,
            'sous_reserve' => 0,
            'optimisation' => 0,
        ];

        foreach ($cases as $case) {
            $status = $case->execution_status;
            if ($status) {
                $stats[$status]++;
                $stats['executed']++;
            }
        }
        
        return $stats;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TestCaseTemplate::class, 'template_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestCaseAssignment::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(TestExecution::class);
    }
}
