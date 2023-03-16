<?php

namespace App\Services\Admin\V1;

use App\Models\ProductItem;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;

class ProductItemCrudService extends BaseCrudService
{
    protected function getModelQuery(): Builder
    {
        return ProductItem::query();
    }
}
