<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\OptionGroupSlug;
use App\Models\ListOption;
use Illuminate\Database\Eloquent\Builder;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

abstract class AbstractFilterAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use CallbackQueryMethods;

    public function __invoke(): void
    {
        $this->sendOptions();
    }

    abstract protected function getListOptionIdsSubQuery(): Builder;

    abstract protected function getOptionGroupSlug(): OptionGroupSlug;

    abstract protected function getSelfBackCommand(): string;

    protected function getCountButtonOnRow(): int
    {
        return 3;
    }

    protected function getMessageText(): string
    {
        $text = "Выберите один или несколько пунктов";
        $text .= "\nПосле того как настроите нужные вам фильтры, нажмите кнопку <b>\"Показать\"</b>";
        $text .= "\nЧтобы сбросить все фильтры нажмите кнопку <b>\"Сбросить\"</b>";

        return $text;
    }

    protected function getAllText(): string
    {
        return 'Любой';
    }

    protected function sendOptions(): void
    {
        $listOptions = ListOption::query()
            ->where('group_slug', $this->getOptionGroupSlug())
            ->whereIn('id', $this->getListOptionIdsSubQuery())
            ->orderBy('weight', 'desc')
            ->orderBy('created_at')
            ->orderBy('id', 'desc')
            ->get();

        $inlineKeyBoard = InlineKeyboardMarkup::make()
            ->addRow(
//                InlineKeyboardButton::make(
//                    'Сбросить',
//                    callback_data: '/filterSet-id=null-back=/products-slug=' . $this->getOptionGroupSlug()->value,
//                ),
                InlineKeyboardButton::make(
                    $this->getAllText(),
                    callback_data: '/filterSet-id=null-back=/products_next-slug=' . $this->getOptionGroupSlug()->value,
                ),
                InlineKeyboardButton::make(
                    'Показать',
                    callback_data: '/products_next',
                ),
            );

        $existsOptionIds = TelegramWebhook::getState()->data->filters->listOptionIds;
        $keyboardRow = [];
        foreach ($listOptions as $listOption) {
            $icon = in_array($listOption->id, $existsOptionIds) ? '✅' : '';
            $keyboardRow[] = InlineKeyboardButton::make(
                "{$icon}{$listOption->title}",
                callback_data: "/filterSet-id={$listOption->id}-back={$this->getSelfBackCommand()}",
            );
            if (count($keyboardRow) === $this->getCountButtonOnRow()) {
                $inlineKeyBoard->addRow(...$keyboardRow);
                $keyboardRow = [];
            }
        }

        if (count($keyboardRow) > 0) {
            $inlineKeyBoard->addRow(...$keyboardRow);
        }

        $currentMessageText = TelegramWebhook::getData()->getMessage()->text;
        $newMessageText = str_replace("\n", ' ', strip_tags($this->getMessageText()));

        if (strcmp($currentMessageText, $newMessageText) > 0) {
            TelegramWebhook::getBot()->sendMessage($this->getMessageText(), [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
                'parse_mode' => ParseMode::HTML,
                'reply_markup' => $inlineKeyBoard,
            ]);
        } else {
            TelegramWebhook::getBot()->editMessageReplyMarkup([
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
                'message_id' => TelegramWebhook::getData()->getMessage()->message_id,
                'reply_markup' => $inlineKeyBoard,
            ]);
        }
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [];
    }

    public static function getPaths(): array
    {
        return ['/^$/ui'];
    }
}
