<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, BelongsToTenant;

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(TestCase::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(TestCaseTemplate::class);
    }
}
