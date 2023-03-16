<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Api\Admin\BaseCrudController;
use App\Services\Admin\V1\ProductCrudService;

class ProductController extends BaseCrudController
{
    public function __construct()
    {
        $this->crudService = app(ProductCrudService::class);
    }
}
