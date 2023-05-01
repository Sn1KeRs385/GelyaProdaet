<?php

namespace App\Bots\Telegram\Actions\System;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Models\TgMessage;
use Carbon\Carbon;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CheckDeleteAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use CallbackQueryMethods;
    use ParamsParse;

    public function __invoke(): void
    {
        $originalText = TelegramWebhook::getData()->getMessage()->getText();
        $date = Carbon::now()->setTimezone('Asia/Yekaterinburg')->toDateTimeString();

        $params = $this->getParamsFromWebhookData(TelegramWebhook::getFacadeRoot());

        $tgMsgId = $params['tgMsgId'] ?? 0;
        $tgMessage = TgMessage::withTrashed()->find($tgMsgId);

        if (!$tgMessage) {
            $text = "{$originalText}\n{$date}: TgMessage не найден!";
            TelegramWebhook::getData()->getMessage()->editText(
                $text,
                [
                    'reply_markup' => InlineKeyboardMarkup::make()
                        ->addRow(
                            InlineKeyboardButton::make(
                                'Удалено',
                                callback_data: "/systemCheckDelete-tgMsgId={$tgMsgId}",
                            ),
                        )
                ]
            );
            return;
        }

        $messageExists = false;
        try {
            TelegramWebhook::getBot()->editMessageCaption([
                'chat_id' => $tgMessage->chat_id,
                'message_id' => $tgMessage->message_id,
                'caption' => TelegramWebhook::getData()->getMessage()->reply_to_message->caption ?? '',
            ]);
            $messageExists = true;
        } catch (\Throwable $exception) {
            if (str_contains($exception->getMessage(), 'message is not modified')) {
                $messageExists = true;
            } elseif (
                !str_contains($exception->getMessage(), 'MESSAGE_ID_INVALID') ||
                !str_contains($exception->getMessage(), 'message to edit not found')
            ) {
                throw $exception;
            }
        }

        if ($messageExists) {
            $text = "{$originalText}\n{$date}: Сообщение еще не удалено!!";
            TelegramWebhook::getData()->getMessage()->editText(
                $text,
                [
                    'reply_markup' => InlineKeyboardMarkup::make()
                        ->addRow(
                            InlineKeyboardButton::make(
                                'Удалено',
                                callback_data: "/systemCheckDelete-tgMsgId={$tgMsgId}",
                            ),
                        )
                ]
            );
            return;
        }

        try {
            TelegramWebhook::getBot()->deleteMessage(
                TelegramWebhook::getData()->getMessage()->reply_to_message->chat->id,
                TelegramWebhook::getData()->getMessage()->reply_to_message->message_id
            );

            TelegramWebhook::getData()->getMessage()->delete();
        } catch (\Throwable $exception) {
            $text = "{$originalText}\n{$date}: Сообщение удалено, но не удалось удалить системные сообщения!";
            TelegramWebhook::getData()->getMessage()->editText($text);
        }
    }

    public static function getPaths(): array
    {
        return ['/^\/systemCheckDelete/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::CALLBACK_QUERY];
    }
}
