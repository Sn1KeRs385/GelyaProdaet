<?php

namespace App\Bots\Telegram\Actions;

use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class StartAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    public function __invoke(): void
    {
        $text = "Приветствую вас! Я бот интернет-магазина детской одежды \"KidStyle72\"."
            . "\nВ данный момент меня активно разрабатывают. "
            . "Как только я буду готов, я смогу помочь вам с выбором лучших нарядов для ваших детей. "
            . "\nСейчас я могу только включить вас в акцию \"Пригласи друга\". "
            . "Просто отправьте мне сообщение с текстом \"Пригласи друга\" или нажмите на следующий текст /promotion_invite_friend"
            . "\nДобро пожаловать в наш магазин!";

        TelegramWebhook::getBot()->sendMessage(
            $text,
            [
                'chat_id' => TelegramWebhook::getData()->getChat()->id
            ]
        );
    }

    public static function getPaths(): array
    {
        return ['/^\/start/u'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE];
    }
}
