<?php

namespace App\Models\Traits\Relations;


use App\Models\File;
use App\Models\ListOption;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property ListOption|null $brand
 * @property ListOption|null $country
 * @property Collection<int, File> $files
 * @property Collection<int, ProductItem> $items
 */
trait ProductRelations
{
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }
}
