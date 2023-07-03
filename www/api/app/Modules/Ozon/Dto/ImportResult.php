<?php

namespace Modules\Ozon\Dto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ImportResult extends Data
{
    /** @var ImportItemResult[] */
    public DataCollection $items;
    public int $total;
}
