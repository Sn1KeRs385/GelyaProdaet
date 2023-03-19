<?php

namespace App\Models\Traits\Attributes;

/**
 * @property string $groupSlugHuman
 */
trait ListOptionAttributes
{
    public function getGroupSlugHumanAttribute(): string
    {
        return __("enums.option_group_slug_human.{$this->group_slug}");
    }
}
