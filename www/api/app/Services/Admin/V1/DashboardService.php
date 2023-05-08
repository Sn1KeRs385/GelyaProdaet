<?php

namespace App\Services\Admin\V1;


use App\Models\ListOption;
use App\Models\ProductItem;
use App\Utils\ColorGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use NumberFormatter;

class DashboardService
{
    public function getMainDashboard(?Carbon $from = null, ?Carbon $to = null): array
    {
        return [
            'sales' => $this->getSalesData($from, $to),
            ...$this->getListOptionData($from, $to),
        ];
    }

    protected function getListOptionData(?Carbon $from = null, ?Carbon $to = null): array
    {
        $products = DB::table('product_items')
            ->select(
                [
                    'product_items.id',
                    'products.type_id',
                    'products.gender_id',
                    'products.brand_id',
                    'products.country_id'
                ]
            )
            ->when($from, function (QueryBuilder $query) use ($from) {
                $query->where('sold_at', '>=', $from);
            })
            ->when($to, function (QueryBuilder $query) use ($to) {
                $query->where('sold_at', '<=', $to);
            })
            ->whereNotNull('sold_at')
            ->leftJoin('products', 'product_items.product_id', '=', 'products.id')
            ->get();

        $listOptionCountFunction = function (array &$idsArray, ?int $id) {
            if (!$id) {
                return;
            }

            if (isset($idsArray[$id])) {
                $idsArray[$id] = $idsArray[$id] + 1;
            } else {
                $idsArray[$id] = 0;
            }
        };

        $typeIds = [];
        $genderIds = [];
        $brandIds = [];
        $countryIds = [];

        foreach ($products as $product) {
            $listOptionCountFunction($typeIds, $product->type_id);
            $listOptionCountFunction($genderIds, $product->gender_id);
            $listOptionCountFunction($brandIds, $product->brand_id);
            $listOptionCountFunction($countryIds, $product->country_id);
        }

        /** @var Collection<int, ListOption>|ListOption[] $listOptions */
        $listOptions = ListOption::query()
            ->select('id', 'title')
            ->orderByDesc('weight')
            ->whereIn('id', array_keys($typeIds))
            ->orWhereIn('id', array_keys($genderIds))
            ->orWhereIn('id', array_keys($brandIds))
            ->orWhereIn('id', array_keys($countryIds))
            ->get();

        $result = [];

        $calcResultFunction = function (array $idsArray, string $type) use (&$result, $listOptions) {
            $resultTemp = [
                'labels' => [],
                'dataset' => [],
                'backgroundColor' => [],
                'total' => 0,
            ];

            $colorGenerator = new ColorGenerator();

            foreach ($idsArray as $id => $count) {
                /** @var ListOption $listOption */
                $listOption = $listOptions->where('id', $id)
                    ->first();
                $resultTemp['labels'][] = $listOption->title ?? '?';
                $resultTemp['dataset'][] = $count;
                $resultTemp['backgroundColor'][] = $colorGenerator->getNextColor(true);
                $resultTemp['total'] = $resultTemp['total'] + $count;
            }

            $result[$type] = $resultTemp;
        };

        $calcResultFunction($typeIds, 'type');
        $calcResultFunction($genderIds, 'gender');
        $calcResultFunction($brandIds, 'brand');
        $calcResultFunction($countryIds, 'country');

        return $result;
    }

    /**
     * @param  Collection<ProductItem>|ProductItem[]  $productItems
     * @return array
     */
    protected function getSalesData(?Carbon $from = null, ?Carbon $to = null): array
    {
        $baseProductsItemsQuery = ProductItem::query()
            ->selectRaw('SUM(price_buy) as price_buy')
            ->selectRaw('SUM(price_sell) as price_sell')
            ->when($from, function (EloquentBuilder $query) use ($from) {
                $query->where('sold_at', '>=', $from);
            })
            ->when($to, function (EloquentBuilder $query) use ($to) {
                $query->where('sold_at', '<=', $to);
            })
            ->whereNotNull('sold_at');

        $groupMethods = ['day', 'month', 'quarter', 'year'];
        $productItems = [];

        foreach ($groupMethods as $index => $groupMethod) {
            $count = (clone $baseProductsItemsQuery)
                ->selectRaw("DATE_TRUNC('{$groupMethod}', sold_at) as sold_at")
                ->groupByRaw("DATE_TRUNC('{$groupMethod}', sold_at)")
                ->count();

            if ($count <= 31 || $index === count($groupMethods) - 1) {
                $productItems = $baseProductsItemsQuery
                    ->selectRaw("DATE_TRUNC('{$groupMethod}', sold_at) as sold_at")
                    ->groupByRaw("DATE_TRUNC('{$groupMethod}', sold_at)")
                    ->orderBy('sold_at')
                    ->get();
                break;
            }
        }

        $labels = [];

        $soldArray = [];
        $soldTotal = 0;
        $earnArray = [];
        $earnTotal = 0;

        $formatter = numfmt_create(config('app.locale'), NumberFormatter::CURRENCY);

        foreach ($productItems as $item) {
            $labels[] = $item->sold_at->timestamp;

            $soldArray[] = $item->priceSellNormalize ?? 0;
            $soldTotal += $item->priceSellNormalize ?? 0;

            $earnArray[] = $item->earnNormalize ?? 0;
            $earnTotal += $item->earnNormalize ?? 0;
        }

        return [
            'labels' => $labels,
            'sold' => [
                'dataset' => $soldArray,
                'total' => numfmt_format_currency($formatter, $soldTotal, "RUR"),
            ],
            'earn' => [
                'dataset' => $earnArray,
                'total' => numfmt_format_currency($formatter, $earnTotal, "RUR"),
            ],
            'group' => $groupMethod,
        ];
    }
}
