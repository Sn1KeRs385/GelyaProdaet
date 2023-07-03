<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\Category;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CategoryTreeResponse extends Data
{
    /** @var Category[] */
    public DataCollection $result;
}
