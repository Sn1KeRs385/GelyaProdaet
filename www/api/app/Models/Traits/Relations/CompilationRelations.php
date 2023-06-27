<?php

namespace App\Models\Traits\Relations;

use App\Models\Compilation;
use App\Models\CompilationListOption;
use App\Models\ListOption;
use App\Models\SitePage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property User|null $user
 * @property Collection<int, ListOption>|ListOption[] $listOptions
 * @property Collection<int, SitePage>|SitePage[] $sitePages
 */
trait CompilationRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(Compilation::class);
    }

    public function listOptions(): BelongsToMany
    {
        return $this->belongsToMany(ListOption::class)
            ->using(CompilationListOption::class);
    }

    public function sitePages(): MorphMany
    {
        return $this->morphMany(SitePage::class, 'owner');
    }
}
