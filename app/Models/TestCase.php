<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TestCase extends Model
{
    use BelongsToTenant, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Ce cas de test a été {$eventName}");
    }

    protected $fillable = [
        'template_id',
        'project_id',
        'data',
        'tenant_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

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
