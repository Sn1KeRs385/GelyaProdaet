<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;

class AttributeValue extends Data
{
    public int $id;
    public string $value;
    public string $info;
    public string $picture;
}
