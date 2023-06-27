<?php

namespace App\Models\Traits\Relations;

use App\Models\SitePage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property SitePage|null $parent
 * @property Collection<int, SitePage>|SitePage[] $children
 * @property Model|null $owner
 */
trait SitePageRelations
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SitePage::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(SitePage::class, 'parent_id');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }
}
