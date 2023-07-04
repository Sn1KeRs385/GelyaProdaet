<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\Stock;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ProductImportStocksResponse extends Data
{
    /** @var Stock[] */
    public DataCollection $result;
}
