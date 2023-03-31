<?php

namespace App\Bots\Telegram\Actions\Product;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\OptionGroupSlug;
use App\Models\Casts\TgUserState\ProductMessageToSend;
use App\Models\ListOption;
use App\Models\Product;
use App\Models\TgMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\DataCollection;

class ProductSearchAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use ParamsParse;
    use CallbackQueryMethods;

    /** @var Collection<int, ListOption>|ListOption[]|false|null */
    protected Collection|false|null $listOptions = false;
    /** @var (Collection<int, ListOption>|ListOption[]|null)[] */
    protected array $listOptionsBySlug = [];

    public function __invoke(): void
    {
        $products = $this->getProducts();

        $tgMessagesToSend = [];
        foreach ($products as $product) {
            /** @var TgMessage $tgMessage */
            $tgMessage = $product->tgMessages[0];
            $tgMessagesToSend[] = ProductMessageToSend::from($tgMessage->toArray());
        }

        $tgMessagesToSend = array_reverse($tgMessagesToSend);

        TelegramWebhook::getState()->data->productMessagesToSend = new DataCollection(
            ProductMessageToSend::class,
            $tgMessagesToSend
        );
        TelegramWebhook::getState()->data->addActionToQueue(app(ProductRequestNextAction::class));
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
            ->whereHas('tgMessages', function (Builder $query) {
                $query->where('is_forward_error', false);
            })
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

    protected function getProducts(): Collection
    {
        return $this->getProductMainQuery()
            ->select('id')
            ->with([
                'tgMessages' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'chat_id',
                            'message_id',
                            'owner_type',
                            'owner_id',
                            'is_forward_error',
                            'created_at'
                        )
                        ->where('is_forward_error', false)
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->get();
    }

    public static function getPaths(): array
    {
        return ['/^$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [];
    }
}
