<?php

namespace App\Jobs;

use App\Bots\Telegram\TelegramBot;
use App\Models\TgMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class TelegramDeleteMessages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected const MAX_TRY_COUNT = 3;
    protected TelegramBot $bot;
    protected int $systemChatId;

    public function __construct(protected int $tgMessageId)
    {
        $this->bot = new TelegramBot(config('telegram.bot_api_key'));
        $this->systemChatId = config('telegram.system_group_id');
    }

    public function handle(): void
    {
        /** @var TgMessage $tgMessage */
        $tgMessage = TgMessage::onlyTrashed()
            ->where('id', $this->tgMessageId)
            ->first();

        if ($tgMessage->is_messages_deleted) {
            return;
        }

        $this->tryDeleteMessage($tgMessage->id, $tgMessage->chat_id, $tgMessage->message_id);

        foreach ($tgMessage->extra_message_ids as $extraMessageId) {
            sleep(3);
            $this->tryDeleteMessage($tgMessage->id, $tgMessage->chat_id, $extraMessageId);
        }

        $tgMessage->is_messages_deleted = true;
        $tgMessage->save();

        sleep(3);
    }

    protected function tryDeleteMessage(int $tgMessageId, int $chatId, int $messageId, int $try = 1): void
    {
        if ($try > self::MAX_TRY_COUNT) {
            return;
        }

        try {
            $this->bot->deleteMessage($chatId, $messageId);
        } catch (\Throwable $exception) {
            echo "Delete {$tgMessageId}:{$messageId} - {$exception->getMessage()}\n";
            if (
                !str_contains($exception->getMessage(), 'message can\'t be deleted')
                && !str_contains($exception->getMessage(), 'Bad Request')
            ) {
                sleep(3);
                $this->tryDeleteMessage($tgMessageId, $chatId, $messageId, $try + 1);
                return;
            }
            $this->tryNotify($tgMessageId, $chatId, $messageId);
        }
    }

    protected function tryNotify(int $tgMessageId, int $chatId, int $messageId, int $try = 1): void
    {
        if ($try > self::MAX_TRY_COUNT) {
            return;
        }

        try {
            $forwardMessage = $this->bot->forwardMessage($this->systemChatId, $chatId, $messageId);
            $this->bot->sendMessage(
                'Не удалось удалить сообщения! @Sn1KeRz, @Linaa0310 - удалите сами',
                [
                    'chat_id' => $this->systemChatId,
                    'reply_to_message_id' => $forwardMessage->message_id,
                    'reply_markup' => InlineKeyboardMarkup::make()
                        ->addRow(
                            InlineKeyboardButton::make(
                                'Удалено',
                                callback_data: "/systemCheckDelete-tgMsgId={$tgMessageId}",
                            ),
                        )
                ]
            );
        } catch (\Throwable $exception) {
            echo "Notify {$tgMessageId}:{$messageId} - {$exception->getMessage()}\n";
            if (!str_contains($exception->getMessage(), 'Bad Request')) {
                sleep(3);
                $this->tryNotify($tgMessageId, $chatId, $messageId, $try + 1);
            }
        }
    }
}
