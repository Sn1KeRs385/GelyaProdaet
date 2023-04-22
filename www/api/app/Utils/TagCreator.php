<?php

namespace App\Utils;


use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class TagCreator
{
    public function __construct(protected WordDeclension $wordDeclension, protected SizeConverter $sizeConverter)
    {
    }

    /**
     * @param  Product  $product
     * @param  Collection<int, ProductItem>|ProductItem[]|null  $items
     * @return string[]
     */
    public function getTagsForProduct(Product $product, Collection $items = null): array
    {
        if (!$items) {
            $items = $product->items()
                ->where(function (Builder $query) {
                    $query->whereHas('size')
                        ->orWhereHas('sizeYear');
                })
                ->with(['size', 'sizeYear'])
                ->where('is_for_sale', true)
                ->where('is_sold', false)
                ->where('is_reserved', false)
                ->get();
        }
        /** @var Collection<int,ProductItem>|ProductItem[] $items */
        if ($items->count() === 0) {
            return [];
        }

        $items = $items->filter(function (ProductItem $item) {
            return $item->is_for_sale && !$item->is_sold && !$item->is_reserved;
        })->values();

        if ($items->count() === 0) {
            return [];
        }

        $tags = [];
        $sizeTags = [];
        $sizeYearTags = [];

        $tagsAddFromText = function (
            string $text,
            array &$tags,
            string $append = '',
            string $prepend = '',
            array $declensions = null
        ) {
            $text = explode(' ', $text)[0] ?? null;
            if (!$text) {
                return;
            }
            $text = explode('-', $text);
            foreach ($text as $textItem) {
                $tagName = "{$append}{$textItem}{$prepend}";
                if ($declensions) {
                    $tagName = "{$append}{$this->wordDeclension->afterNumDeclension($textItem, $declensions)}{$prepend}";
                }
                $tags[$tagName] = true;
            }
        };

        foreach ($items as $item) {
            if ($item->price_final && $item->price_final > 0) {
                $tags['распродажа'] = true;
            }
            if ($item->size) {
                $sizeText = explode(' ', $item->size->title)[0] ?? null;
                $tagsAddFromText($sizeText, $sizeTags, '', 'рост');
                $years = $this->sizeConverter->getYearFromSize($sizeText);
                foreach ($years as $year) {
                    $tagsAddFromText($year, $sizeYearTags, '', '', ['год', 'года', 'лет']);
                }
            }
            if ($item->sizeYear) {
                $sizeYearText = explode(' ', $item->sizeYear->title)[0] ?? null;
                $tagsAddFromText($sizeYearText, $sizeYearTags, '', '', ['год', 'года', 'лет']);
                $sizes = $this->sizeConverter->getSizeFromYear($sizeYearText);
                foreach ($sizes as $size) {
                    $tagsAddFromText($size, $sizeTags, '', 'рост');
                }
            }
        }

        $tags[mb_strtolower($product->gender->title)] = true;
        if ($product->brand) {
            $tags[mb_strtolower($product->brand->title)] = true;
        }
        $tags[mb_strtolower($product->type->title)] = true;

        $tagsFinal = array_keys($tags);

        if (!empty($sizeTags)) {
            $tagsTemp = Arr::sort(array_keys($sizeTags));
            foreach ($tagsTemp as $tagTemp) {
                $tagsFinal[] = $tagTemp;
            }
        }

        if (!empty($sizeYearTags)) {
            $tagsTemp = Arr::sort(array_keys($sizeYearTags));
            foreach ($tagsTemp as $tagTemp) {
                $tagsFinal[] = $tagTemp;
            }
        }

        return $tagsFinal;
    }
}
