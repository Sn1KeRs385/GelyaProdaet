<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Enums\OptionGroupSlug;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Builder;

class FilterSizeAction extends AbstractFilterAction
{
    public static function getPaths(): array
    {
        return ['/^\/filterSize$/ui'];
    }

    protected function getListOptionIdsSubQuery(): Builder
    {
        $productQuery = Product::query()
            ->select('id')
            ->whereHas('tgMessages');;
        return ProductItem::query()
            ->select('size_id')
            ->whereIn('product_id', $productQuery)
            ->where('is_sold', false)
            ->where('is_for_sale', true);
    }

    protected function getOptionGroupSlug(): OptionGroupSlug
    {
        return OptionGroupSlug::SIZE;
    }

    protected function getSelfBackCommand(): string
    {
        return '/filterSize';
    }

    protected function getMessageText(): string
    {
        $text = "Выберите один или несколько размеров";
        $text .= "\nПосле того как настроите нужные вам размеры, нажмите кнопку <b>\"Показать\"</b>";
        $text .= "\nЧтобы увидеть товары с любым размером, нажмите кнопку <b>\"Сбросить\"</b>";

        return $text;
    }

    protected function getAllText(): string
    {
        return 'Выбрать все размеры';
    }

    protected function getCountButtonOnRow(): int
    {
        return 4;
    }
}
