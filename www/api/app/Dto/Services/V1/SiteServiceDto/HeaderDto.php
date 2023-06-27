<?php

namespace App\Dto\Services\V1\SiteServiceDto;


use App\Models\Compilation;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;


class HeaderDto extends Data
{
    /** @var Collection<Compilation> */
    public Collection $compilationLinks;
}
