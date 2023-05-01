<?php

namespace App\Bots\Telegram\Actions\Default;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\Permission;
use App\Models\TgMessage;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class EmptyMessageHandlerAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use CallbackQueryMethods;
    use ParamsParse;

    public function __invoke(): void
    {
        $this->forwardCheck();
    }

    protected function forwardCheck(): bool
    {
        $result = $this->forwardAdminCheck();
        return $result;
    }

    protected function forwardAdminCheck(): bool
    {
        if (!TelegramWebhook::getUser()->checkPermissionTo(Permission::ADMIN_MENU_ACCESS->value)) {
            return false;
        }

        $fromChatId = TelegramWebhook::getData()->getMessage()->forward_from_chat->id ?? 0;
        if ((int)$fromChatId !== (int)config('telegram.public_id')) {
            return false;
        }

        $messageId = TelegramWebhook::getData()->getMessage()->forward_from_message_id ?? 0;
        $tgMessage = TgMessage::query()
            ->where('message_id', $messageId)
            ->orWhereRaw("extra_message_ids @> '$messageId'")
            ->first();
        if (!$tgMessage) {
            TelegramWebhook::getBot()->sendMessage('Не удалось найти эту запись в базе :(', [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
            ]);
            return false;
        }

        $showUrl = implode(
            '/',
            [config('app.admin_url'), strtolower($tgMessage->owner_type) . 's', $tgMessage->owner_id]
        );

        TelegramWebhook::getBot()->sendMessage('Мне удалось найти это сообщение в базе', [
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'reply_to_message_id' => TelegramWebhook::getData()->getMessage()->message_id,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(
                        'Просмотр',
                        url: $showUrl,
                    ),
                )
        ]);

        return true;
    }

    public static function getPaths(): array
    {
        return [];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE];
    }
}
