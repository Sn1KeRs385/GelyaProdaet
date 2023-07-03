<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\AttributesWithCategoryId;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AttributesResponse extends Data
{
    /** @var AttributesWithCategoryId[] */
    public DataCollection $result;
}
