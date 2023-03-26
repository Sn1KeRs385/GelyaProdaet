<?php

namespace App\Models\Traits\Relations;


use App\Models\TgReferralLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property User $user
 * @property null|TgReferralLink $link
 */
trait TgReferralLinkJoinRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(TgReferralLink::class);
    }
}
