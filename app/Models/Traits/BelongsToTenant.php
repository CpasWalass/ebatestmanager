<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait
     */
    public static function bootBelongsToTenant()
    {
        // Automatically scope queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenancy()->tenant) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', tenant('id'));
            }
        });

        // Automatically set tenant_id on create
        static::creating(function ($model) {
            if (tenancy()->tenant && !$model->tenant_id) {
                $model->tenant_id = tenant('id');
            }
        });
    }

    /**
     * Get the route key for the model
     */
    public function getTenantId()
    {
        return $this->tenant_id;
    }

    /**
     * Scope to a specific tenant
     */
    public function scopeForTenant(Builder $query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Remove tenant scope (for admin purposes)
     */
    public function scopeWithoutTenantScope(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }
}
