<?php

namespace App\Models\Traits\Scopes;


use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|self selectForSite()
 */
trait ProductItemScopes
{
    public function scopeSelectForSite($query): void
    {
        $query->select(
            'id',
            'product_id',
            'size_id',
            'color_id',
            'price',
            'price_final',
            'is_sold',
            'count',
            'size_year_id',
            'is_reserved'
        );
    }
}
