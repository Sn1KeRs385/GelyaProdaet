<?php

namespace App\Bots\Telegram\Actions\Product;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Filters\FilterGenderFormatChooseAction;
use App\Bots\Telegram\Actions\Filters\FilterSizeAction;
use App\Bots\Telegram\Actions\Filters\FilterSizeFormatChooseAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class ProductRequestNextAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    public function __invoke(): void
    {
        $step = TelegramWebhook::getState()->data->productRequestStep + 1;
        TelegramWebhook::getState()->data->productRequestStep = $step;

        switch ($step) {
            case 1:
                TelegramWebhook::getState()->data->addActionToQueue(app(FilterGenderFormatChooseAction::class));
                TelegramWebhook::getState()->data->addActionToQueue(app(FilterSizeAction::class));
                break;
            default:
                TelegramWebhook::getState()->data->addActionToQueue(app(FilterSizeFormatChooseAction::class));
                TelegramWebhook::getState()->data->addActionToQueue(app(ProductIndexAction::class));
                break;
        }
    }

    public static function getPaths(): array
    {
        return ['/^\/products_next$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::CALLBACK_QUERY];
    }
}
