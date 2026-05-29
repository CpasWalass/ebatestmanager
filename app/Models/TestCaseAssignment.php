<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCaseAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'test_case_id',
        'user_id',
        'scope',
        'specific_fields',
        'status',
    ];

    protected $casts = [
        'specific_fields' => 'array',
    ];

    public function testCase(): BelongsTo
    {
        return $this->belongsTo(TestCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(TestExecution::class, 'assignment_id');
    }
}
