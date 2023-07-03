<?php

namespace Modules\Ozon\Dto\Responses;

use Modules\Ozon\Dto\TaskId;
use Spatie\LaravelData\Data;

class ProductImportResponse extends Data
{
    public TaskId $result;
}
