<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestExecution extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'test_case_id',
        'tester_id',
        'assignment_id',
        'results',
        'status',
        'comments',
        'tenant_id',
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
