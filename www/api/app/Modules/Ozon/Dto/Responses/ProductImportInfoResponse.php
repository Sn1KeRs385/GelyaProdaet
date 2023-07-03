<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\ImportResult;
use Spatie\LaravelData\Data;

class ProductImportInfoResponse extends Data
{
    public ImportResult $result;
}
