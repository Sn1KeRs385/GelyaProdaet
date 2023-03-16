<?php

namespace App\Services\Admin\V1;

use App\Models\ListOption;
use App\Services\Admin\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;

class ListOptionCrudService extends BaseCrudService
{
    protected function getModelQuery(): Builder
    {
        return ListOption::query();
    }
}
