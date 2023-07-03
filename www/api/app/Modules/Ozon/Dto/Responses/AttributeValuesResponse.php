<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\AttributeValue;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AttributeValuesResponse extends Data
{
    /** @var AttributeValue[] */
    public DataCollection $result;

    public bool $has_next;
}
