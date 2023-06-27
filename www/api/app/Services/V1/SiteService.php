<?php

namespace App\Services\V1;


use App\Dto\Services\V1\SiteServiceDto\FooterDto;
use App\Dto\Services\V1\SiteServiceDto\HeaderDto;
use App\Dto\Services\V1\SiteServiceDto\IndexPageDto;
use App\Enums\OptionGroupSlug;
use App\Models\Compilation;
use App\Models\File;
use App\Models\ListOption;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;

class SiteService
{
    public function getIndexPageData(): IndexPageDto
    {
        $productTypes = ListOption::query()
            ->selectForSite()
            ->where('group_slug', OptionGroupSlug::PRODUCT_TYPE)
            ->orderBy('weight', 'desc')
            ->orderBy('created_at')
            ->orderBy('id', 'desc')
            ->with([
                'files' => function (MorphMany|File $query) {
                    $query->selectForSite()
                        ->where('status', FileStatus::FINISHED);
                },
            ])
            ->take(8)
            ->get();

        $lastProducts = Product::query()
            ->selectForSite()
            ->whereHas('items', function (Builder $query) {
                $query->where('is_sold', false)
                    ->where('is_for_sale', true);
            })
            ->with([
                'type' => function (BelongsTo|ListOption $query) {
                    $query->selectForSite();
                },
                'gender' => function (BelongsTo|ListOption $query) {
                    $query->selectForSite();
                },
                'brand' => function (BelongsTo|ListOption $query) {
                    $query->selectForSite();
                },
                'country' => function (BelongsTo|ListOption $query) {
                    $query->selectForSite();
                },
                'files' => function (MorphMany|File $query) {
                    $query->selectForSite()
                        ->where('status', FileStatus::FINISHED);
                },
                'items' => function (HasMany|ProductItem $query) {
                    $query->selectForSite()
                        ->where('is_for_sale', true)
                        ->with([
                            'size' => function (BelongsTo|ListOption $query) {
                                $query->selectForSite();
                            },
                            'sizeYear' => function (BelongsTo|ListOption $query) {
                                $query->selectForSite();
                            },
                            'color' => function (BelongsTo|ListOption $query) {
                                $query->selectForSite();
                            },
                        ]);
                }
            ])
            ->whereHas('tgMessages')
            ->orderByDesc('created_at')
            ->take(14)
            ->get();

        $compilations = Compilation::query()
            ->where('is_show_on_main_page', true)
            ->with(['listOptions'])
            ->get();

        foreach ($compilations as $compilation) {
            $compilation->products = $compilation->productQuery
                ->selectForSite()
                ->with([
                    'type' => function (BelongsTo|ListOption $query) {
                        $query->selectForSite();
                    },
                    'gender' => function (BelongsTo|ListOption $query) {
                        $query->selectForSite();
                    },
                    'brand' => function (BelongsTo|ListOption $query) {
                        $query->selectForSite();
                    },
                    'country' => function (BelongsTo|ListOption $query) {
                        $query->selectForSite();
                    },
                    'files' => function (MorphMany|File $query) {
                        $query->selectForSite()
                            ->where('status', FileStatus::FINISHED);
                    },
                    'items' => function (HasMany|ProductItem $query) {
                        $query->selectForSite()
                            ->where('is_for_sale', true)
                            ->with([
                                'size' => function (BelongsTo|ListOption $query) {
                                    $query->selectForSite();
                                },
                                'sizeYear' => function (BelongsTo|ListOption $query) {
                                    $query->selectForSite();
                                },
                                'color' => function (BelongsTo|ListOption $query) {
                                    $query->selectForSite();
                                },
                            ]);
                    }
                ])
                ->orderByDesc('created_at')
                ->take(14)
                ->get();
        }

        return IndexPageDto::from([
            'productTypes' => $productTypes,
            'lastProducts' => $lastProducts,
            'compilations' => $compilations,
            'headerData' => $this->getHeaderData(),
            'footerData' => $this->getFooterData(),
        ]);
    }

    public function getHeaderData(): HeaderDto
    {
        $compilationLinks = Compilation::query()
            ->where('is_show_on_header', true)
            ->with(['sitePages'])
            ->whereHas('sitePages')
            ->get();

        return HeaderDto::from([
            'compilationLinks' => $compilationLinks,
        ]);
    }

    public function getFooterData(): FooterDto
    {
        $compilationLinks = Compilation::query()
            ->where('is_show_on_footer', true)
            ->with(['sitePages'])
            ->whereHas('sitePages')
            ->get();

        return FooterDto::from([
            'compilationLinks' => $compilationLinks,
        ]);
    }
}
