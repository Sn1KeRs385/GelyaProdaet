<?php

namespace App\Models\Traits\Scopes;


use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|self selectForSite()
 */
trait ListOptionScopes
{

    public function scopeSelectForSite($query): void
    {
        $query->select('id', 'group_slug', 'title', 'weight');
    }
}
