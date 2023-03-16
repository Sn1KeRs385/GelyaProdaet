<?php

namespace App\Services\Admin;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            if (!isset($data["relation_{$relationMapper['name']}"])) {
                continue;
            }
            /** @var BaseCrudService $crudService */
            $crudService = app($relationMapper['crudService']);
            foreach ($data["relation_{$relationMapper['name']}"] as $relationData) {
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

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $this->storeDataHook($data);

            $model = $this->getModelQuery()->make($data);

            $this->storeBeforeSaveHook($model, $data);

            $model->save();

            $this->storeAfterSaveHook($model, $data);

            $this->storeOrUpdateRelationData($model, $data);

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
