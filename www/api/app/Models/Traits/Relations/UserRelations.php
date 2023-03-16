<?php

namespace App\Models\Traits\Relations;


use App\Models\File;
use App\Models\UserCode;
use App\Models\UserIdentifier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<int, UserIdentifier> $identifiers
 * @property Collection<int, UserCode> $codes
 * @property Collection<int, File> $files
 */
trait UserRelations
{
    public function identifiers(): HasMany
    {
        return $this->hasMany(UserIdentifier::class);
    }

    public function codes(): HasMany
    {
        return $this->hasMany(UserCode::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }
}
