<?php

namespace App\Models\Casts\TgUserState;

use Spatie\LaravelData\Data;

class ProductMessageToSend extends Data
{
    public int $id;
    public int $chat_id;
    public int $message_id;
}
