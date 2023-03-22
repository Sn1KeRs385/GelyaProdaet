<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductItem;
use App\Models\TgMessage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class ProductService
{
    public function sendProductToTelegram(Product $product, string|int $chatId = null): void
    {
        if (!$product->items()->where('is_for_sale', true)->exists()) {
            return;
        }
        if (!$chatId) {
            if (is_numeric(config('telegram.public_id'))) {
                $chatId = config('telegram.public_id');
            } else {
                $chatId = '@' . config('telegram.public_id');
            }
        }

        $bot = new Nutgram(config('telegram.bot_api_key'));

        /** @var TgMessage $message */
        $message = $product->tgMessages()
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($message) {
            $bot->editMessageCaption([
                'chat_id' => $message->chat_id,
                'message_id' => $message->message_id,
                'caption' => $this->getTgMessageText($product),
            ]);
        } else {
            $inputMediaPhoto = [];
            foreach ($product->files as $file) {
                $photo = new InputMediaPhoto();
                $photo->type = 'photo';
                $photo->media = $file->url;
//                $photo->media = 'https://shop-cdn1.vigbo.tech/shops/173204/products/20950271/images/3-2efe41fcd3f4c89971fc7bd505632f89.jpg?version=1';
                if (count($inputMediaPhoto) === 0) {
                    $photo->caption = $this->getTgMessageText($product);
                } else {
                    $photo->caption = '';
                }
                $photo->parse_mode = ParseMode::HTML;
                $photo->has_spoiler = false;
                $inputMediaPhoto[] = $photo;
            }

            /** @var Message[] $response */
            $response = $bot->sendMediaGroup($inputMediaPhoto, [
                'chat_id' => $chatId,
            ]);
            $product->tgMessages()->create([
                'chat_id' => $chatId,
                'message_id' => $response[0]->message_id,
                'file_ids' => $product->files->pluck('id'),
            ]);
        }
    }

    protected function getTgMessageText(Product $product): string
    {
        $sizes = [];

        /** @var ProductItem[] $items */
        $items = $product->items()
            ->with(['size', 'color'])
            ->where('is_for_sale', true)
            ->get();

        if (count($items) === 0) {
            throw new \Exception('ProductItem is empty to send');
        }

        $price = $items[0]->price ?? 0;

        foreach ($items as $item) {
            if ($item->price > $price) {
                $price = $item->price;
            }
            if (!isset($sizes[$item->size->title])) {
                $sizes[$item->size->title] = [
                    'for_sale' => 0,
                    'is_sold' => 0,
                    'colors' => [],
                ];
            }
            if ($item->is_sold) {
                $sizes[$item->size->title]['is_sold'] = $sizes[$item->size->title]['is_sold'] + 1;
            } else {
                $sizes[$item->size->title]['for_sale'] = $sizes[$item->size->title]['for_sale'] + 1;

                if ($item->color?->title && !in_array($item->color->title, $sizes[$item->size->title]['colors'])) {
                    $sizes[$item->size->title]['colors'][] = $item->color->title;
                }
            }
        }

        $gender = mb_strtolower($product->gender->title);
        $text = "{$product->type->title} $gender";

        if ($product->brand) {
            $text .= " {$product->brand->title}";
        }

        if ($product->country) {
            $text .= " ({$product->country->title})";
        }

        $text .= "\n{$product->title}";

        if ($product->description) {
            $text .= "\n$product->description";
        }

        $text .= "\n\nРазмеры: ";
        foreach ($sizes as $size => $info) {
            $text .= "\n$size";
            if ($info['for_sale'] === 0) {
                $text .= " ❌";
            }
            if (count($info['colors']) > 0) {
                $text .= ": " . implode(', ', $info['colors']);
            }
        }

        $text .= "\n\nЦена: " . round($price / 100, 2);

        return $text;
    }
}
