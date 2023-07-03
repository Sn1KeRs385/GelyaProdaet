<?php

namespace App\Models\Traits\Relations;


use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Product $product
 */
trait OzonDataRelations
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
