<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AttributesWithCategoryId extends Data
{
    public int $category_id;
    /** @var Attribute[] */
    public DataCollection $attributes;
}
