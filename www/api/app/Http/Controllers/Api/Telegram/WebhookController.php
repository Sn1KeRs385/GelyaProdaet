<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Bots\Telegram\Actions\ActionContract;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Bots\Telegram\TelegramActionRouter;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class WebhookController
{
    public function __invoke(TelegramActionRouter $actionRouter)
    {
        /** @var Collection<int, ActionContract> $actions */
        $actions = $actionRouter->getAction(TelegramWebhook::getData());

        $actions->each(function (ActionContract $action) {
            $action->__invoke();
        });


        for ($i = 0; $i < 3; $i++) {
            $nextAction = TelegramWebhook::getState()->data->getNextAction();
            if (!$nextAction) {
                break;
            }
            $nextAction->__invoke();
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
