<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestCase extends Model
{
    protected $fillable = [
        'project_id',
        'template_id',
        'data',
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
