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
use App\Services\ProductService;
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

    public function __construct(protected ProductService $productService)
    {
    }

    public function __invoke(): void
    {
        $params = $this->getParamsFromWebhookData(TelegramWebhook::getFacadeRoot());
        $page = $params['page'] ?? 1;

        $this->deleteCallbackQueryMessage(TelegramWebhook::getFacadeRoot());

        $this->sendProducts((int)$page);
    }

    protected function sendProducts(int $page): void
    {
        $perPage = 1;

        $products = $this->getProducts($page, $perPage);

        $sizes = $this->getFilterListOptionsBySlug(OptionGroupSlug::SIZE);
        $brands = $this->getFilterListOptionsBySlug(OptionGroupSlug::BRAND);
        $countries = $this->getFilterListOptionsBySlug(OptionGroupSlug::COUNTRY);
        $genders = $this->getFilterListOptionsBySlug(OptionGroupSlug::GENDER);

        $filterText = 'Размер: ' . ($sizes ? implode(', ', $sizes->pluck('title')->toArray()) : 'любой');
        $filterText .= "\nБренд: " . ($brands ? implode(', ', $brands->pluck('title')->toArray()) : 'любой');
        $filterText .= "\nСтрана: " . ($countries ? implode(', ', $countries->pluck('title')->toArray()) : 'любой');
        $filterText .= $genders ? implode(', ', $genders->pluck('title')->toArray()) : "\nНа мальчика и девочку";
        if ($products->count() > 0 && $page === 1) {
            $text = '🔽🔽🔽🔽🔽🔽🔽🔽🔽🔽🔽🔽🔽';
            $text .= "\nТовары по вашему запросу:";
            $text .= "\n$filterText";
            TelegramWebhook::getBot()->sendMessage($text, [
                'chat_id' => TelegramWebhook::getData()->getChat()->id,
            ]);
        }

        foreach ($products as $product) {
            $tgMessage = $product->tgMessages[0];
            TelegramWebhook::getBot()->forwardMessage(
                TelegramWebhook::getData()->getChat()->id,
                $tgMessage->chat_id,
                $tgMessage->message_id,
            );
        }

        $count = 0;
        $pageCount = 0;
        $inlineKeyBoard = InlineKeyboardMarkup::make();
        if ($products->count() === 0) {
            $text = "По вашему запросу не найдено товаров.";
        } else {
            $count = $this->getProductMainQuery()->count();
            $pageCount = ceil($count / $perPage);
            $buttons = [];
//            if ($page > 1) {
//                $prevPage = $page - 1;
//                $buttons[] = InlineKeyboardButton::make('<-Назад', callback_data: "/products-page={$prevPage}");
//            }
            if ($page < $pageCount) {
                $nextPage = $page + 1;
                $buttons[] = InlineKeyboardButton::make('Показать еще', callback_data: "/products-page={$nextPage}");
            }
            if (count($buttons) > 0) {
                $inlineKeyBoard->addRow(...$buttons);
            }
            $text = "Ваш запрос:";
        }
        $text .= "\n$filterText";
        if ($page < $pageCount) {
            $productRemained = $count - ($page * $perPage);
            $text .= "\n\nЕще не показано товаров: {$productRemained}";
            $text .= "\nНажмите кнопку \"Показать еще\" чтобы посмотреть больше";
        } else {
            $text .= "\n\nПо вашему запросу показаны все товары.";
        }

        TelegramWebhook::getBot()->sendMessage($text, [
            'chat_id' => TelegramWebhook::getData()->getChat()->id,
            'reply_markup' => $inlineKeyBoard
                ->addRow(
                    InlineKeyboardButton::make(
                        'Выбрать бренд',
                        callback_data: '/filterBrand'
                    ),
                    InlineKeyboardButton::make(
                        'Выбрать страну',
                        callback_data: '/filterCountry'
                    ),
                )
                ->addRow(
                    InlineKeyboardButton::make(
                        'Выбрать размер',
                        callback_data: '/filterSize'
                    ),
                    InlineKeyboardButton::make(
                        'Выбрать пол',
                        callback_data: '/filterGender'
                    ),
                )
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
        return ['/^\/products/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE, UpdateTypes::CALLBACK_QUERY];
    }
}
