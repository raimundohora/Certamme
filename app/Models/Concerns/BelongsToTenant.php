<?php
namespace App\Models\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model) {
            if (! $model->tenant_id) {
                $model->tenant_id = Tenant::current()?->getKey() ?? Tenant::query()->value('id');
            }
        });

        static::addGlobalScope('tenant', function (Builder $q) {
            if ($tenant = Tenant::current()) {
                $q->where($q->getModel()->getTable().'.tenant_id', $tenant->getKey());
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
