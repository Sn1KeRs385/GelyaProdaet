<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class Category extends Data
{
    public int $category_id;
    public string $title;

    /** @var Category[] */
    public DataCollection $children;
}
