<?php

namespace App\Services\Admin\V1;

use App\Models\File;
use App\Models\ListOption;
use App\Services\Admin\BaseCrudService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ListOptionCrudService extends BaseCrudService
{
    protected array $fileFields = [
        'file_ids',
        'logo_ids',
    ];

    protected function getModelQuery(): Builder
    {
        return ListOption::query();
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
        $query->with(['logo']);
    }

    protected function showAfterQueryExecHook(Model &$model): void
    {
        /** @var ListOption $model */
        $model->logo->each(function (File $file) {
            $file->setAppends(['url']);
        });
    }

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
        $query->orderBy('group_slug')
            ->orderBy('weight', 'desc')
            ->orderBy('title')
            ->orderBy('id', 'desc');
    }

    protected function indexAfterPaginateHook(LengthAwarePaginator|Collection &$paginate): void
    {
        $paginate->each(function (ListOption $listOption) {
            $listOption->setAppends(['group_slug_human']);
        });
    }

    protected function allAfterGetHook(Collection &$collection): void
    {
        $collection->each(function (ListOption $listOption) {
            $listOption->setAppends(['group_slug_human']);
        });
    }

    protected function allBeforeQueryExecHook(Builder &$query): void
    {
        $query->orderBy('group_slug')
            ->orderBy('weight', 'desc')
            ->orderBy('title')
            ->orderBy('id', 'desc');
    }
}
