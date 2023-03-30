<?php

namespace App\Bots\Telegram\Actions\Product;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\OptionGroupSlug;
use App\Models\ListOption;
use App\Models\Product;
use App\Utils\WordDeclension;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ProductIndexAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use ParamsParse;
    use CallbackQueryMethods;

    /** @var Collection<int, ListOption>|ListOption[]|false|null */
    protected Collection|false|null $listOptions = false;
    /** @var (Collection<int, ListOption>|ListOption[]|null)[] */
    protected array $listOptionsBySlug = [];

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

        $products = $this->getProducts($page, $perPage);

        foreach ($products as $product) {
            $tgMessage = $product->tgMessages[0];
            TelegramWebhook::getBot()->forwardMessage(
                TelegramWebhook::getData()->getChat()->id,
                $tgMessage->chat_id,
                $tgMessage->message_id,
            );
        }

        $count = $this->getProductMainQuery()->count();
        $pageCount = (int)ceil($count / $perPage);

        if ($products->count() === 0) {
            $text = "По вашему запросу не найдено товаров. Попробуйте изменить запрос";
            TelegramWebhook::getBot()->sendMessage($text, [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
            ]);
            return;
        }

        if ($page === $pageCount) {
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

        $productRemained = $count - ($page * $perPage);

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

    /**
     * @return Collection<int, ListOption>|ListOption[]|null
     */
    protected function getFilterListOptions(): ?Collection
    {
        if ($this->listOptions !== false) {
            return $this->listOptions;
        }

        if (count(TelegramWebhook::getState()->data->filters->listOptionIds) === 0) {
            $this->listOptions = null;
        }

        if ($this->listOptions === false) {
            $this->listOptions = ListOption::query()
                ->whereIn('id', TelegramWebhook::getState()->data->filters->listOptionIds)
                ->get();

            if ($this->listOptions->count() === 0) {
                $this->listOptions = null;
            }
        }

        return $this->listOptions;
    }

    /**
     * @return Collection<int, ListOption>|ListOption[]|null
     */
    protected function getFilterListOptionsBySlug(OptionGroupSlug $slug): ?Collection
    {
        $filterListOptions = $this->getFilterListOptions();
        if (!$filterListOptions) {
            return null;
        }

        if (isset($this->listOptionsBySlug[$slug->value])) {
            return $this->listOptionsBySlug[$slug->value];
        }

        $filteredOptions = $filterListOptions->filter(function (ListOption $option) use ($slug) {
            return $option->group_slug === $slug->value;
        });

        if (count($filteredOptions) === 0) {
            $filteredOptions = null;
        }

        $this->listOptionsBySlug[$slug->value] = $filteredOptions;
        return $this->listOptionsBySlug[$slug->value];
    }

    protected function getProductMainQuery(): Builder
    {
        $sizes = $this->getFilterListOptionsBySlug(OptionGroupSlug::SIZE);
        $brands = $this->getFilterListOptionsBySlug(OptionGroupSlug::BRAND);
        $countries = $this->getFilterListOptionsBySlug(OptionGroupSlug::COUNTRY);
        $genders = $this->getFilterListOptionsBySlug(OptionGroupSlug::GENDER);
        return Product::query()
            ->orderBy('created_at', 'desc')
            ->whereHas('items', function (Builder $query) use ($sizes) {
                $query->where('is_sold', false)
                    ->where('is_for_sale', true)
                    ->when($sizes, function (Builder $query) use ($sizes) {
                        $query->whereIn('size_id', $sizes->pluck('id'));
                    });
            })
            ->whereHas('tgMessages')
            ->when($brands, function (Builder $query) use ($brands) {
                $query->whereIn('brand_id', $brands->pluck('id'));
            })
            ->when($countries, function (Builder $query) use ($countries) {
                $query->whereIn('country_id', $countries->pluck('id'));
            })
            ->when($genders, function (Builder $query) use ($genders) {
                $query->whereIn('gender_id', $genders->pluck('id'));
            });
    }

    protected function getProducts(int $page, int $perPage): Collection
    {
        return $this->getProductMainQuery()
            ->with([
                'tgMessages' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();
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
