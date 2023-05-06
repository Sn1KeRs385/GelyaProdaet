<?php

namespace App\Models\Traits\Relations;

use App\Enums\FileCollection;
use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<int, File>|File[] $files
 * @property Collection<int, File>|File[] $logo
 */
trait ListOptionRelations
{
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }
}
