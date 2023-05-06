<?php

namespace App\Models\Traits\Relations;

use App\Enums\FileCollection;
use App\Enums\OptionGroupSlug;
use App\Models\File;
use App\Models\ListOption;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Product $product
 * @property ListOption|null $size
 * @property ListOption|null $sizeYear
 * @property ListOption|null $color
 */
trait ProductItemRelations
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::SIZE);
    }

    public function sizeYear(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::COLOR);
    }
}
