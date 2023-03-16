<?php

namespace App\Models\Traits\Relations;


use App\Models\ListOption;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ListOption $product
 * @property ListOption $size
 * @property ListOption|null $color
 */
trait ProductItemRelations
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }
}
