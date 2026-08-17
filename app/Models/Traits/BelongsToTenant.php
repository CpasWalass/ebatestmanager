<?php

namespace App\Models\Traits;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (! $model->tenant_id) {
                $model->tenant_id = config('app.default_tenant_id', 'eba');
            }
        });
    }
}
