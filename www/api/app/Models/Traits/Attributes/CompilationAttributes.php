<?php

namespace App\Models\Traits\Attributes;

use App\Enums\OptionGroupSlug;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Builder $productQuery
 */
trait CompilationAttributes
{
    public function getProductQueryAttribute(): Builder
    {
        $productWhere = [
            'type_id' => [],
            'gender_id' => [],
            'brand_id' => [],
            'country_id' => [],
        ];
        $productItemWhere = [
            'size_id' => [],
            'size_year_id' => [],
            'color_id' => [],
        ];

        foreach ($this->listOptions as $listOption) {
            switch (OptionGroupSlug::tryFrom($listOption->group_slug)) {
                case OptionGroupSlug::PRODUCT_TYPE:
                    $productWhere['type_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::GENDER:
                    $productWhere['gender_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::BRAND:
                    $productWhere['brand_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::COUNTRY:
                    $productWhere['country_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::SIZE:
                    $productItemWhere['size_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::SIZE_YEAR:
                    $productItemWhere['size_year_id'][] = $listOption->id;
                    break;
                case OptionGroupSlug::COLOR:
                    $productItemWhere['color_id'][] = $listOption->id;
                    break;
            };
        }

        $query = Product::query();

        foreach ($productWhere as $field => $ids) {
            if (count($ids) === 0) {
                continue;
            }

            $query->whereIn($field, $ids);

//            $query->where(function(Builder $query) use($field, $ids){
//               foreach($ids as $index => $id) {
//                   if($index === 0) {
//                       $query->where($field, $id)
//                   }
//               }
//            });
        }

        $query->whereHas('items', function (Builder $query) use ($productItemWhere) {
            $query->where('is_sold', false)
                ->where('is_for_sale', true);

            foreach ($productItemWhere as $field => $ids) {
                if (count($ids) === 0) {
                    continue;
                }

                $query->whereIn($field, $ids);
            }
        });

        return $query;
    }
}
