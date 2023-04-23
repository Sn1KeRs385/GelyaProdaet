<?php

namespace App\Bots\Telegram\Actions\Product;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Models\Casts\TgUserState\ProductMessageToSend;
use App\Models\TgMessage;
use App\Utils\WordDeclension;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Spatie\LaravelData\DataCollection;

class ProductIndexAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use ParamsParse;
    use CallbackQueryMethods;

    public function __construct(protected WordDeclension $wordDeclension)
    {
    }

    public function __invoke(): void
    {
        $params = $this->getParamsFromWebhookData(TelegramWebhook::getFacadeRoot());
        $page = (int)($params['page'] ?? 1);

        if ($page > 1) {
            $this->deleteCallbackQueryMessage(TelegramWebhook::getFacadeRoot());
        }

        $this->sendProducts($page);
    }

    protected function sendProducts(int $page): void
    {
        $perPage = 1;

        $productMessagesToSendState = TelegramWebhook::getState()->data->productMessagesToSend->all();

        /** @var ProductMessageToSend[] $productMessages */
        $productMessages = [];

        for ($i = 0; $i < $perPage; $i++) {
            $message = array_pop($productMessagesToSendState);
            if (!$message) {
                break;
            }
            $productMessages[] = $message;
        }

        TelegramWebhook::getState()->data->productMessagesToSend = new DataCollection(
            ProductMessageToSend::class,
            $productMessagesToSendState
        );

        if (count($productMessages) === 0) {
            $text = "По вашему запросу не найдено товаров. Попробуйте изменить запрос";
            TelegramWebhook::getBot()->sendMessage($text, [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
            ]);
            return;
        }

        $productRemained = TelegramWebhook::getState()->data->productMessagesToSend->count();

        foreach ($productMessages as $productMessage) {
            try {
                TelegramWebhook::getBot()->forwardMessage(
                    TelegramWebhook::getData()->getChat()->id,
                    $productMessage->chat_id,
                    $productMessage->message_id,
                );
            } catch (TelegramException $exception) {
                if (str_contains($exception->getMessage(), 'message to forward not found')) {
                    TgMessage::query()
                        ->where('id', $productMessage->id)
                        ->update(['is_not_found_error' => true]);
                }
            }
        }

        if ($productRemained === 0) {
            $text = "Мы показали все товары по вашему запросу.";
            TelegramWebhook::getBot()->sendMessage($text, [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
            ]);
            return;
        }

        $inlineKeyBoard = InlineKeyboardMarkup::make();

        $buttons = [];
        $buttons[] = InlineKeyboardButton::make(
            'Показать еще',
            callback_data: '/products_index-page=' . $page + 1,
        );

        $inlineKeyBoard->addRow(...$buttons);

        $productText = $this->wordDeclension->afterNumDeclension(
            $productRemained,
            ['товар', 'товара', 'товаров'],
            false
        );
        $text = "Мы нашли еще {$productRemained} $productText. Нажмите на кнопку и мы продолжим :)";

        TelegramWebhook::getBot()->sendMessage($text, [
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'reply_markup' => $inlineKeyBoard,
        ]);
    }

    public static function getPaths(): array
    {
        return ['/^\/products_index/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::CALLBACK_QUERY];
    }
}
