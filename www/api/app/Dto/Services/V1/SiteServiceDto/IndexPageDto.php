<?php

namespace App\Dto\Services\V1\SiteServiceDto;


use App\Models\Compilation;
use App\Models\ListOption;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;


class IndexPageDto extends Data
{
    /** @var Collection<ListOption> */
    public Collection $productTypes;

    /** @var Collection<Product> */
    public Collection $lastProducts;

    /** @var Collection<Compilation> */
    public Collection $compilations;

    public HeaderDto $headerData;
    public FooterDto $footerData;
}
