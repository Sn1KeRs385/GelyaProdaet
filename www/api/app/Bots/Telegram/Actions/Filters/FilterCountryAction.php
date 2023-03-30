<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Enums\OptionGroupSlug;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class FilterCountryAction extends AbstractFilterAction
{
    public static function getPaths(): array
    {
        return ['/^\/filterCountry$/ui'];
    }

    protected function getListOptionIdsSubQuery(): Builder
    {
        return Product::query()
            ->select('country_id')
            ->whereHas('items', function (Builder $query) {
                $query->where('is_sold', false)
                    ->where('is_for_sale', true);
            })
            ->whereHas('tgMessages');
    }

    protected function getOptionGroupSlug(): OptionGroupSlug
    {
        return OptionGroupSlug::COUNTRY;
    }

    protected function getSelfBackCommand(): string
    {
        return '/filterCountry';
    }

    protected function getMessageText(): string
    {
        $text = "Выберите одну или несколько стран";
        $text .= "\nПосле того как настроите нужные вам страны, нажмите кнопку <b>\"Показать\"</b>";
        $text .= "\nЧтобы увидеть товары из любой страны, нажмите кнопку <b>\"Сбросить\"</b>";

        return $text;
    }
}
