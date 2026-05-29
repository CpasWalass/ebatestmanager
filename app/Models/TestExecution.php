<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestExecution extends Model
{
    protected $fillable = [
        'test_case_id',
        'assignment_id',
        'tester_id',
        'results',
        'status',
        'comments',
    ];

    protected $casts = [
        'results' => 'array',
    ];

    public function testCase(): BelongsTo
    {
        return $this->belongsTo(TestCase::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestCaseAssignment::class);
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tester_id');
    }
}
