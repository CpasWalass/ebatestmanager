<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope queries to the current tenant
     */
    public function scopeForCurrent($query)
    {
        if ($tenantId = tenant('id')) {
            return $query->where('tenant_id', $tenantId);
        }

        return $query;
    }

    /**
     * Get the domain associated with this tenant
     */
    public function getDomain()
    {
        return $this->data['domain'] ?? null;
    }

    /**
     * Set the domain for this tenant
     */
    public function setDomain($domain)
    {
        $data = $this->data ?? [];
        $data['domain'] = $domain;
        $this->data = $data;
        return $this;
    }
}
