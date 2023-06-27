<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\ListOption;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;

class ProductRepository
{
    public function paginateListForSite(int $perPage = 25, int $page = 1): \Illuminate\Contracts\Pagination\Paginator
    {
        /** @var Paginator|Product[] $products */
        $products = Product::query()
            ->selectForSite()
            ->whereHas('items', function (Builder $query) {
                $query->where('is_sold', false)
                    ->where('is_for_sale', true);
            })
            ->whereHas('files', function (Builder $query) {
                $query->where('status', FileStatus::FINISHED);
            })
            ->with([
                'files' => function (MorphMany|File $query) {
                    $query->selectForSite()
                        ->where('status', FileStatus::FINISHED);
                },
                'items' => function (HasMany|ProductItem $query) {
                    $query->selectForSite()
                        ->where('is_for_sale', true)
                        ->where('is_sold', false);
                }
            ])
            ->simplePaginate($perPage, ['*'], 'page', $page);

        $listOptionIds = [];
        foreach ($products as $product) {
            /** @var Product $product */
            $listOptionIds[$product->type_id] = true;
            $listOptionIds[$product->gender_id] = true;
            if ($product->brand_id) {
                $listOptionIds[$product->brand_id] = true;
            }
            if ($product->country_id) {
                $listOptionIds[$product->country_id] = true;
            }
            foreach ($product->items as $item) {
                $listOptionIds[$item->size_id] = true;
                if ($item->color_id) {
                    $listOptionIds[$item->color_id] = true;
                }
            }
        }

        $listOptions = ListOption::query()
            ->selectForSite()
            ->whereIn('id', Arr::sort(array_keys($listOptionIds)))
            ->get();

        foreach ($products as $product) {
            /** @var Product $product */
            $product->setAttribute('type', $listOptions->firstWhere('id', $product->type_id));
            $product->setAttribute('gender', $listOptions->firstWhere('id', $product->gender_id));
            if ($product->brand_id) {
                $product->setAttribute('brand', $listOptions->firstWhere('id', $product->brand_id));
            }
            if ($product->country_id) {
                $product->setAttribute('country', $listOptions->firstWhere('id', $product->country_id));
            }
            foreach ($product->items as $item) {
                $item->setAttribute('size', $listOptions->firstWhere('id', $item->size_id));
                if ($item->color_id) {
                    $item->setAttribute('color', $listOptions->firstWhere('id', $item->color_id));
                }
            }
        }

        return $products;
    }
}
