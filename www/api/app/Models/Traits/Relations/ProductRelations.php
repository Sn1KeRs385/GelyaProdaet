<?php

namespace App\Models\Traits\Relations;


use App\Models\File;
use App\Models\ListOption;
use App\Models\OzonData;
use App\Models\ProductItem;
use App\Models\TgMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property ListOption $type
 * @property ListOption $gender
 * @property ListOption|null $brand
 * @property ListOption|null $country
 * @property Collection<int, File> $files
 * @property Collection<int, ProductItem>|ProductItem[] $items
 * @property Collection<int, TgMessage>|TgMessage[] $tgMessages
 * @property OzonData $ozonData
 */
trait ProductRelations
{
    public function type(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(ListOption::class);
    }

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

    public function tgMessages(): MorphMany
    {
        return $this->morphMany(TgMessage::class, 'owner');
    }

    public function ozonData(): HasOne
    {
        return $this->hasOne(OzonData::class);
    }
}
