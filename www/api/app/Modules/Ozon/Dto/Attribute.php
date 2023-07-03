<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;

class Attribute extends Data
{
    public int $id;
    public string $name;
    public string $description;
    public string $type;
    public bool $is_collection;
    public bool $is_required;
    public int $group_id;
    public string $group_name;
    public int $dictionary_id;
    public bool $is_aspect;
    public bool $category_dependent;
}
