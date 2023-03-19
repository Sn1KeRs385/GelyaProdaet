<?php

namespace App\Services\Admin;

use App\Models\File;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

abstract class BaseCrudService
{
    /**
     * @var array
     * [
     *  [
     *    'name' => 'users' //Relation name on model
     *    'crudService' => 'UserCrudService::class // CrudService
     *    'foreignKeu' => 'user_id' //FK key for adding
     *  ],
     *  [...],
     *  ...
     * ]
     */
    protected array $relationMapper = [];

    protected array $fileFields = [
        'files_ids',
    ];

    abstract protected function getModelQuery(): Builder;

    protected function indexBeforeQueryExecHook(Builder &$query): void
    {
    }

    protected function indexAfterPaginateHook(LengthAwarePaginator &$paginate): void
    {
    }

    public function index(int $page, int $perPage): LengthAwarePaginator
    {
        $query = $this->getModelQuery();

        $this->indexBeforeQueryExecHook($query);

        $paginate = $query->paginate(perPage: $perPage, page: $page);

        $this->indexAfterPaginateHook($paginate);

        return $paginate;
    }

    protected function allBeforeQueryExecHook(Builder &$query): void
    {
    }

    protected function allAfterGetHook(Collection &$collection): void
    {
    }

    public function all(): Collection
    {
        $query = $this->getModelQuery();

        $this->allBeforeQueryExecHook($query);

        $items = $query->get();

        $this->allAfterGetHook($items);

        return $items;
    }

    protected function storeDataHook(array &$data): void
    {
    }

    protected function storeBeforeSaveHook(Model &$model, array $data): void
    {
    }

    protected function storeAfterSaveHook(Model &$model, array &$data): void
    {
    }

    protected function storeOrUpdateRelationData(Model $model, array $data): void
    {
        foreach ($this->relationMapper as $relationMapper) {
            if (!isset($data[$relationMapper['name']])) {
                continue;
            }
            /** @var BaseCrudService $crudService */
            $crudService = app($relationMapper['crudService']);
            foreach ($data[$relationMapper['name']] as $relationData) {
                if (isset($relationData['id'])) {
                    $crudService->update($relationData['id'], $relationData);
                } else {
                    /** @var Product $model */
                    $relationData[$relationMapper['foreignKey']] = $model->id;
                    $crudService->store($relationData);
                }
            }
        }
    }

    protected function processFiles(Model $model, array $data): void
    {
        $filesIds = [];
        foreach ($this->fileFields as $fileField) {
            if (!isset($data[$fileField])) {
                return;
            }
            $filesIds = [...$filesIds, ...$data[$fileField]];
        }

        $filesNotInModel = File::query()
            ->whereIn('id', $filesIds)
            ->where(function (Builder $query) use ($model) {
                $query->where('owner_type', '<>', $model->getMorphClass())
                    ->orWhere('owner_id', '<>', $model->id);
            })
            ->get();

        foreach ($filesNotInModel as $file) {
            if (!Gate::allows('update', $file)) {
                continue;
            }
            $file->owner_type = $model->getMorphClass();
            $file->owner_id = $model->id;
            $file->save();
        }

        $filesToDelete = File::query()
            ->whereNotIn('id', $filesIds)
            ->where('owner_type', $model->getMorphClass())
            ->where('owner_id', $model->id)
            ->get();

        foreach ($filesToDelete as $file) {
            $file->delete();
        }
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $this->storeDataHook($data);

            $model = $this->getModelQuery()->make($data);

            $this->storeBeforeSaveHook($model, $data);

            $model->save();

            $this->storeAfterSaveHook($model, $data);

            $this->storeOrUpdateRelationData($model, $data);

            $this->processFiles($model, $data);

            return $model;
        });
    }

    public function show(string $id)
    {
        //
    }

    public function update(string $id, array $data)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
