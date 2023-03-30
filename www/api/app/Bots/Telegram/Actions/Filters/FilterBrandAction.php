<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Enums\OptionGroupSlug;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class FilterBrandAction extends AbstractFilterAction
{
    public static function getPaths(): array
    {
        return ['/^\/filterBrand$/ui'];
    }

    protected function getListOptionIdsSubQuery(): Builder
    {
        return Product::query()
            ->select('brand_id')
            ->whereHas('items', function (Builder $query) {
                $query->where('is_sold', false)
                    ->where('is_for_sale', true);
            })
            ->whereHas('tgMessages');
    }

    protected function getOptionGroupSlug(): OptionGroupSlug
    {
        return OptionGroupSlug::BRAND;
    }

    protected function getSelfBackCommand(): string
    {
        return '/filterBrand';
    }

    protected function getMessageText(): string
    {
        $text = "Выберите один или несколько брендов";
        $text .= "\nПосле того как настроите нужные вам бренды, нажмите кнопку <b>\"Показать\"</b>";
        $text .= "\nЧтобы увидеть товары любых брендов, нажмите кнопку <b>\"Сбросить\"</b>";

        return $text;
    }
}
