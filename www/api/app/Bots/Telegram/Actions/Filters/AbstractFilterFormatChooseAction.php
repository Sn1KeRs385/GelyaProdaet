<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\OptionGroupSlug;
use App\Models\ListOption;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

abstract class AbstractFilterFormatChooseAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    abstract protected function getOptionGroupSlug(): OptionGroupSlug;

    public function __invoke(): void
    {
        TelegramWebhook::getBot()->editMessageReplyMarkup([
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'message_id' => TelegramWebhook::getData()->getMessage()->message_id,
            'reply_markup' => InlineKeyboardMarkup::make(),
        ]);
        if (count(TelegramWebhook::getState()->data->filters->listOptionIds) === 0) {
            TelegramWebhook::getBot()->editMessageText("{$this->getLabelText()}{$this->getAllText()}", [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
                'message_id' => TelegramWebhook::getData()->getMessage()->message_id,
            ]);
            return;
        }

        $options = ListOption::query()
            ->where('group_slug', $this->getOptionGroupSlug())
            ->whereIn('id', TelegramWebhook::getState()->data->filters->listOptionIds)
            ->orderBy('weight', 'desc')
            ->orderBy('created_at')
            ->orderBy('id', 'desc')
            ->get();

        if ($options->count() === 0) {
            TelegramWebhook::getBot()->editMessageText("{$this->getLabelText()}{$this->getAllText()}", [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
                'message_id' => TelegramWebhook::getData()->getMessage()->message_id,
            ]);
            return;
        }

        $text = $this->getLabelText() . implode(', ', $options->pluck('title')->toArray());
        TelegramWebhook::getBot()->editMessageText($text, [
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'message_id' => TelegramWebhook::getData()->getMessage()->message_id,
        ]);
    }

    protected function getAllText(): string
    {
        return 'Все';
    }

    protected function getLabelText(): string
    {
        return 'Вы выбрали: ';
    }

    public static function getPaths(): array
    {
        return ['/^$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [];
    }
}
