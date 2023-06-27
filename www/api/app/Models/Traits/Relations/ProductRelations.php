<?php

namespace App\Models\Traits\Relations;

use App\Enums\OptionGroupSlug;
use App\Models\File;
use App\Models\ListOption;
use App\Models\ProductItem;
use App\Models\SitePage;
use App\Models\TgMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property ListOption $type
 * @property ListOption $gender
 * @property ListOption|null $brand
 * @property ListOption|null $country
 * @property Collection<int, File>|File[] $files
 * @property Collection<int, ProductItem>|ProductItem[] $items
 * @property Collection<int, TgMessage>|TgMessage[] $tgMessages
 */
trait ProductRelations
{
    public function type(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::PRODUCT_TYPE);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::GENDER);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::BRAND);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(ListOption::class)
            ->where('group_slug', OptionGroupSlug::COUNTRY);
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

    public function sitePages(): MorphMany
    {
        return $this->morphMany(SitePage::class, 'owner');
    }
}
