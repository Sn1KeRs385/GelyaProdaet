<?php

namespace App\Services;

use App\Bots\Telegram\TelegramBot;
use App\Enums\IdentifierType;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\TgMessage;
use App\Models\User;
use App\Models\UserIdentifier;
use App\Utils\TagCreator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

class ProductService
{
    public function __construct(protected TagCreator $tagCreator)
    {
    }

    public function resendToTelegram(Product $product): Product
    {
        $product->tgMessages->each(function (TgMessage $tgMessage) {
            $tgMessage->delete();
        });

        $product->touch();

        return $product;
    }

    public function sendToUserTelegram(Product $product, User $user): void
    {
        /** @var UserIdentifier $userTelegramIdentifier */
        $userTelegramIdentifier = $user->identifiers()
            ->where('type', IdentifierType::TG_USER_ID)
            ->first();

        if (!$userTelegramIdentifier) {
            throw new \Exception('Идентификатор телеграма не найден');
        }

        $this->sendProductToTelegramWithNoSaveAndCheck($product, $userTelegramIdentifier->value);
    }

    public function sendProductToTelegram(Product $product, string|int $chatId = null): void
    {
        $config = [
            'timeout' => 120, // default in seconds, when contacting the Telegram API
        ];

        $bot = new TelegramBot(config('telegram.bot_api_key'), $config);

        if (!$chatId) {
            $chatId = $bot::getPublicId();
        }

        /** @var TgMessage $message */
        $message = $product->tgMessages()
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($message) {
            try {
                $bot->editMessageCaption([
                    'chat_id' => $message->chat_id,
                    'message_id' => $message->message_id,
                    'caption' => $this->getTgMessageText($product),
                    'parse_mode' => ParseMode::HTML,
                ]);
            } catch (\Throwable $exception) {
                if (str_contains($exception->getMessage(), 'message to edit not found')) {
                    $message->is_not_found_error = true;
                    $message->save();
                }
                if (!str_contains($exception->getMessage(), 'message is not modified')) {
                    throw $exception;
                }
            }
            return;
        }
        if (!$product->send_to_telegram) {
            return;
        }

        $inputMediaPhoto = [];
        foreach ($product->files as $file) {
            $photo = new InputMediaPhoto();
            $photo->type = 'photo';
            $photo->media = $file->url;
            if (count($inputMediaPhoto) === 0) {
                $photo->caption = $this->getTgMessageText($product);
            } else {
                $photo->caption = '';
            }
            $photo->parse_mode = ParseMode::HTML;
            $photo->has_spoiler = false;
            $inputMediaPhoto[] = $photo;
        }

        $disableNotification = true;
        $now = Carbon::now();
        $start = Carbon::createFromTimeString('4:00');
        $end = Carbon::createFromTimeString('18:00');
        if ($now->between($start, $end)) {
            $disableNotification = false;
        }

        /** @var Message[] $response */
        $response = $bot->sendMediaGroup($inputMediaPhoto, [
            'chat_id' => $chatId,
            'disable_notification' => $disableNotification,
            'parse_mode' => ParseMode::HTML,
        ]);

        $messageIds = [];
        foreach ($response as $index => $message) {
            if ($index === 0) {
                continue;
            }
            $messageIds[] = $message->message_id;
        }

        $product->tgMessages()->create([
            'chat_id' => $chatId,
            'message_id' => $response[0]->message_id,
            'file_ids' => $product->files->pluck('id'),
            'extra_message_ids' => $messageIds
        ]);
    }

    public function sendProductToTelegramWithNoSaveAndCheck(Product $product, string|int $chatId): array
    {
        $bot = new TelegramBot(config('telegram.bot_api_key'));

        $inputMediaPhoto = [];
        foreach ($product->files as $file) {
            $photo = new InputMediaPhoto();
            $photo->type = 'photo';
            $photo->media = $file->url;
            if (count($inputMediaPhoto) === 0) {
                $photo->caption = $this->getTgMessageText($product);
            } else {
                $photo->caption = '';
            }
            $photo->parse_mode = ParseMode::HTML;
            $photo->has_spoiler = false;
            $inputMediaPhoto[] = $photo;
        }

        $disableNotification = true;
        $now = Carbon::now();
        $start = Carbon::createFromTimeString('4:00');
        $end = Carbon::createFromTimeString('18:00');
        if ($now->between($start, $end)) {
            $disableNotification = false;
        }

        /** @var Message[] $response */
        $response = $bot->sendMediaGroup($inputMediaPhoto, [
            'chat_id' => $chatId,
            'disable_notification' => $disableNotification,
            'parse_mode' => ParseMode::HTML,
        ]);

        $showUrl = implode(
            '/',
            [config('app.admin_url'), strtolower($product->getMorphClass()) . 's', $product->id]
        );

        $bot->sendMessage('Управление', [
            'chat_id' => $chatId,
            'reply_to_message_id' => $response[0]->message_id,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(
                        'В админку',
                        url: $showUrl,
                    ),
                )
        ]);

        return $response;
    }

    protected function getTgMessageText(Product $product): string
    {
        $sizes = [];

        /** @var ProductItem[] $items */
        $items = $product->items()
            ->with(['size', 'sizeYear', 'color'])
            ->get();

        if (count($items) === 0) {
            throw new \Exception('ProductItem is empty to send');
        }

        $price = $items[0]->price ?? 0;
        $priceFinal = $items[0]->price_final ?? null;
        $onePriceOnAllItems = true;
        $hasSizes = false;
        $hasColor = false;

        foreach ($items as $item) {
            if ($item->price !== $price) {
                $onePriceOnAllItems = false;
            }
            if ($item->price > $price) {
                $price = $item->price;
            }

            if ($item->price_final !== $priceFinal) {
                $onePriceOnAllItems = false;
            }
            if ($item->price_final > $priceFinal) {
                $priceFinal = $item->price_final;
            }

            $colorAsKey = false;

            if ($item->sizeYear) {
                $key = $item->sizeYear->title;
                if ($item->size) {
                    $key = "{$key} ({$item->size->title})";
                }
                $hasSizes = true;
            } elseif ($item->size) {
                $key = $item->size->title;
                $hasSizes = true;
            } elseif ($item->color) {
                $key = $item->color->title;
                $colorAsKey = true;
                $hasColor = true;
            } else {
                $key = '-';
            }

            if ($item->count > 1) {
                $key = "{$key}-{$item->count} шт";
            }
            if (!isset($sizes[$key])) {
                $sizes[$key] = [
                    'weight' => $item->sizeYear->weight ?? $item->size->weight ?? $item->color->weight ?? 0,
                    'title' => $item->sizeYear->title ?? $item->size->title ?? $item->color->title ?? '-',
                    'id' => $item->sizeYear->id ?? $item->size->id ?? $item->color->id ?? $item->id,

                    'for_sale' => 0,
                    'is_sold' => 0,
                    'is_no_reserved' => 0,
                    'colors' => [],
                    'price' => $item->price,
                    'price_final' => $item->price_final,
                ];
            }

            $itemIsSold = $item->is_sold || !$item->is_for_sale;
            if ($itemIsSold) {
                $sizes[$key]['is_sold'] = $sizes[$key]['is_sold'] + 1;
            } else {
                $sizes[$key]['for_sale'] = $sizes[$key]['for_sale'] + 1;
            }

            if (!$item->is_reserved) {
                $sizes[$key]['is_no_reserved'] = $sizes[$key]['is_no_reserved'] + 1;
            }

            if (!$colorAsKey && $item->color?->title && ($sizes[$key]['colors'][$item->color->title] ?? '') !== 'for_sale') {
                if (!$itemIsSold) {
                    if (!$item->is_reserved) {
                        $sizes[$key]['colors'][$item->color->title] = 'for_sale';
                    } else {
                        $sizes[$key]['colors'][$item->color->title] = 'reserved';
                    }
                } elseif (($sizes[$key]['colors'][$item->color->title] ?? '') !== 'reserved') {
                    $sizes[$key]['colors'][$item->color->title] = 'sold';
                }
            }
        }
        $sizes = collect($sizes)->sortBy([
            ['weight', 'desc'],
            ['title', 'asc'],
            ['id', 'desc'],
        ]);

        $gender = mb_strtolower($product->gender->title);
        $text = "{$product->type->title} $gender";

        if ($product->brand) {
            $text .= " {$product->brand->title}";
        }

        if ($product->country) {
            $text .= " ({$product->country->title})";
        }

//        $text .= "\n{$product->title}";

        if ($product->description) {
            $text .= "\n$product->description";
        }

        if ($hasSizes && !$hasColor) {
            $text .= "\n\nРазмеры: ";
        } elseif (!$hasSizes && $hasColor) {
            $text .= "\n\nЦвета: ";
        } elseif ($hasSizes && $hasColor) {
            $text .= "\n\nРазмеры и цвета: ";
        }

        if ($hasSizes || $hasColor) {
            foreach ($sizes as $size => $info) {
                if (count($info['colors']) === 0) {
                    if ($info['for_sale'] > 0) {
                        if ($info['is_no_reserved'] === 0) {
                            $text .= "\n⚠️{$size}<b>(бронь)</b>";
                        } else {
                            $text .= "\n✅{$size}";
                        }
                    } else {
                        $text .= "\n❌<s>{$size}</s>";
                    }
                } else {
                    $text .= "\n{$size}";
                    $colorsForSale = [];
                    $colorsReserved = [];
                    $colorsSold = [];
                    foreach ($info['colors'] as $color => $status) {
                        switch ($status) {
                            case 'for_sale':
                                $colorsForSale[] = "✅{$color}";
                                break;
                            case 'reserved':
                                $colorsReserved[] = "⚠️{$color}<b>(бронь)</b>";
                                break;
                            default:
                                $colorsSold[] = "❌<s>{$color}</s>";
                        }
                    }
                    $text .= ": " . implode(', ', [...$colorsForSale, ...$colorsReserved, ...$colorsSold]);
                }

                if (!$onePriceOnAllItems) {
                    $price = round($info['price'] / 100, 2);
                    if ($info['price_final'] && $info['price_final'] > 0) {
                        $priceFinal = round($info['price_final'] / 100, 2);
                        $text .= " - <s>{$price} ₽</s> {$priceFinal} ₽";
                    } else {
                        $text .= " - {$price} ₽";
                    }
                }
            }
        } else {
            $hasForSale = false;
            foreach ($sizes as $size => $info) {
                if ($info['for_sale'] > 0) {
                    $hasForSale = true;
                    break;
                }
            }

            if ($hasForSale) {
                $text .= "\n\n✅ В наличии!";
            } else {
                $text .= "\n\n❌ Распродано!";
            }
        }

        if ($onePriceOnAllItems) {
            $text .= "\n\nЦена: ";
            $price = round($price / 100, 2);
            if ($priceFinal && $priceFinal > 0) {
                $priceFinal = round($priceFinal / 100, 2);
                $text .= "<s>{$price} ₽</s> {$priceFinal} ₽";
            } else {
                $text .= "{$price} ₽";
            }
        }

        $tags = $this->tagCreator->getTagsForProduct($product, $items);

        if (!empty($tags)) {
            $text .= "\n\n"
                . implode(
                    ' ',
                    Arr::map($tags, fn(string $tag) => preg_replace('/[^0-9A-zА-я#_]/u', '', "#$tag"))
                );
        }

        return $text;
    }
}
