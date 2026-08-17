<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Project extends Model
{
    use BelongsToTenant, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'description', 'version'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => 'Ce projet a été '.match ($eventName) {
                'created' => 'créé',
                'updated' => 'mis à jour',
                'deleted' => 'supprimé',
                'restored' => 'restauré',
                default => $eventName,
            });
    }

    protected $fillable = [
        'client_id',
        'created_by',
        'name',
        'description',
        'version',
        'perimeter',
        'type',
        'status',
        'start_date',
        'end_date',
        'tenant_id',
        'links',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'links' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class)->latest();
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(TestCase::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(TestCaseTemplate::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TestCaseAssignment::class);
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function isUat(): bool
    {
        return $this->type === 'uat';
    }

    /**
     * Statistiques agrégées des cas de test de ce projet.
     * Source UNIQUE utilisée par tous les dashboards et les rapports générés
     * (auparavant recalculée différemment à 3 endroits, avec des résultats
     * parfois divergents pour les mêmes données).
     */
    public function stats(): array
    {
        return TestCase::statsFor($this->testCases()->get());
    }
}
