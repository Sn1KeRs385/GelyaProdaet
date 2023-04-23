<?php

namespace App\Observers;

use App\Jobs\TelegramDeleteMessages;
use App\Models\TgMessage;

class TgMessageObserver
{
    public function deleted(TgMessage $tgMessage): void
    {
        TelegramDeleteMessages::dispatch($tgMessage->id)->onQueue('tg_system_notification')->afterCommit();
    }

    public function restoring(TgMessage $tgMessage): void
    {
        throw new \Exception('Нельзя восстановить сообщение');
    }
}
