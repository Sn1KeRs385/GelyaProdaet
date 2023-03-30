<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\OptionGroupSlug;
use App\Models\ListOption;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class FilterGenderAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use CallbackQueryMethods;

    public function __invoke(): void
    {
        $this->deleteCallbackQueryMessage(TelegramWebhook::getFacadeRoot());

        $this->sendOptions();
    }

    protected function sendOptions(): void
    {
        $listOptions = ListOption::query()
            ->where('group_slug', OptionGroupSlug::GENDER)
            ->where('is_hidden_from_user_filters', false)
            ->get();

        $inlineKeyBoard = InlineKeyboardMarkup::make();

        $keyboardRow = [];
        foreach ($listOptions as $listOption) {
            $keyboardRow[] = InlineKeyboardButton::make(
                $listOption->title,
                callback_data: "/filterSet-id={$listOption->id}-back=/products_next-slug=" . OptionGroupSlug::GENDER->value,
            );
            if (count($keyboardRow) === 3) {
                $inlineKeyBoard->addRow(...$keyboardRow);
                $keyboardRow = [];
            }
        }

        if (count($keyboardRow) > 0) {
            $inlineKeyBoard->addRow(...$keyboardRow);
        }

        $inlineKeyBoard->addRow(
            InlineKeyboardButton::make(
                "И на мальчика и на девочку",
                callback_data: '/filterSet-id=null-back=/products_next-slug=' . OptionGroupSlug::GENDER->value,
            ),
        );

        $text = "На кого вы хотели бы посмотреть вещи?";

        TelegramWebhook::getBot()->sendMessage($text, [
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'parse_mode' => ParseMode::HTML,
            'reply_markup' => $inlineKeyBoard,
        ]);
    }

    public static function getPaths(): array
    {
        return ['/^\/filterGender$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [];
    }
}
