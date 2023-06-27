<?php

namespace App\Models\Traits\Scopes;


use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|self selectForSite()
 */
trait ProductScopes
{

    public function scopeSelectForSite($query): void
    {
        $query->select('id', 'title', 'description', 'type_id', 'gender_id', 'brand_id', 'country_id');
    }
}
