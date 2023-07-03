<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;

class ImportItemResult extends Data
{
    public string $offer_id;
    public int $product_id;
    public string $status;
    public array $errors;
}
