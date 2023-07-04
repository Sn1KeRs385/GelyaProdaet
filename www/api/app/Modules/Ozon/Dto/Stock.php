<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;

class Stock extends Data
{
    public array $errors;
    public string $offer_id;
    public int $product_id;
    public bool $updated;
}
