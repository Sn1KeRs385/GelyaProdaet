<?php

namespace App\Models\Traits\Relations;


use App\Models\Album;
use App\Models\AlbumFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Collection<int, Album> $albums
 */
trait FileRelations
{
    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class)
            ->using(AlbumFile::class);
    }
}
