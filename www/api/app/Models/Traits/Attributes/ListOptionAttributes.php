<?php

namespace App\Models\Traits\Attributes;

use App\Enums\FileCollection;
use App\Models\File;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property string $groupSlugHuman
 * @property Collection<int, File>|File[] $logo
 */
trait ListOptionAttributes
{
    public function getGroupSlugHumanAttribute(): string
    {
        return __("enums.option_group_slug_human.{$this->group_slug}");
    }

    /**
     * @return Collection<int, File>|File[]
     */
    public function getLogoAttribute(): Collection
    {
        return $this->files
            ->where('collection', FileCollection::LIST_OPTION_LOGO->value)
            ->values();
    }
}
