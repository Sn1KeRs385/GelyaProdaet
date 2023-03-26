<?php

namespace App\Models\Traits\Relations;


use App\Models\TgReferralLinkJoin;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property User $user
 * @property Collection<int, TgReferralLinkJoin> $linkJoins
 */
trait TgReferralLinkRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkJoins(): HasMany
    {
        return $this->hasMany(TgReferralLinkJoin::class, 'link_id');
    }
}
