<?php

namespace App\Services;

use App\Models\Casts\TgUserState\UserStateData;
use App\Models\TgUserState;
use Illuminate\Support\Facades\Cache;

class TgUserStateService
{
    const USER_STATE_KEY = "user-state";

    public function getState(int $userId): TgUserState
    {
        if (Cache::has(self::USER_STATE_KEY . "-{$userId}")) {
            return Cache::get(self::USER_STATE_KEY . "-{$userId}");
        }

        $state = TgUserState::firstWhere('user_id', $userId);

        if (!$state) {
            $state = new TgUserState();
            $state->user_id = $userId;
            $state->data = UserStateData::from([]);
            $state->save();
        }

        return $state;
    }

    public function saveState(TgUserState $state): void
    {
        $state->save();
        Cache::set(self::USER_STATE_KEY . "-{$state->user_id}", $state, 1800);
    }
}
