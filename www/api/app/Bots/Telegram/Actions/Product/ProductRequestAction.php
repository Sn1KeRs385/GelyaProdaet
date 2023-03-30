<?php

namespace App\Bots\Telegram\Actions\Product;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Filters\FilterGenderAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class ProductRequestAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    public function __invoke(): void
    {
        TelegramWebhook::getState()->data->filters->listOptionIds = [];
        TelegramWebhook::getState()->data->productRequestStep = 0;
        TelegramWebhook::getState()->data->addActionToQueue(app(FilterGenderAction::class));
    }

    public static function getPaths(): array
    {
        return ['/^\/products$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE];
    }
}
