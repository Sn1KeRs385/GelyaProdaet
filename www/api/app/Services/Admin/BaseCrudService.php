<?php

namespace App\Services\Admin;

use App\Models\File;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;
use Sn1KeRs385\FileUploader\App\Jobs\UploaderJob;

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
    protected array $hasManyRelationMapper = [];

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

    public function index(
        int $page,
        int $perPage,
        ?string $orderBy = null,
        ?bool $orderDesc = null
    ): LengthAwarePaginator {
        $query = $this->getModelQuery()
            ->when($orderBy, function ($query) use ($orderBy, $orderDesc) {
                $query->orderBy($orderBy, $orderDesc ? 'DESC' : 'ASC');
            });

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
        foreach ($this->hasManyRelationMapper as $hasManyRelationMapper) {
            if (!isset($data[$hasManyRelationMapper['name']])) {
                continue;
            }
            /** @var BaseCrudService $crudService */
            $crudService = app($hasManyRelationMapper['crudService']);

            $existsId = [];
            foreach ($data[$hasManyRelationMapper['name']] as $relationData) {
                if (isset($relationData['id'])) {
                    $relationModel = $crudService->update($relationData['id'], $relationData);
                } else {
                    $relationData[$hasManyRelationMapper['foreignKey']] = $model->id;
                    $relationModel = $crudService->store($relationData);
                }
                $existsId[] = $relationModel->id;
            }

            $model->{$hasManyRelationMapper['name']}()
                ->whereNotIn('id', $existsId)
                ->chunk(500, function (Collection $relationModels) use ($crudService) {
                    foreach ($relationModels as $relationModel) {
                        /** @var Model $relationModel */
                        $crudService->destroy($relationModel->id);
                    }
                });
        }

        $model->touch();
    }

    protected function processFiles(Model $model, array $data): void
    {
        $filesIds = [];
        foreach ($this->fileFields as $fileField) {
            if (!isset($data[$fileField])) {
                continue;
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

        if (!filter_var(config('uploader.start_uploader_job_on_finish'), FILTER_VALIDATE_BOOLEAN)) {
            $filesToUploadProcess = File::query()
                ->where('owner_type', $model->getMorphClass())
                ->where('owner_id', $model->id)
                ->where('status', FileStatus::CREATED)
                ->get();

            foreach ($filesToUploadProcess as $file) {
                UploaderJob::dispatch($file->id, $model)->onQueue(config('uploader.queue'));
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

            $this->processFiles($model, $data);

            return $model;
        });
    }

    protected function showBeforeQueryExecHook(Builder &$query): void
    {
    }

    protected function showAfterQueryExecHook(Model &$model): void
    {
    }

    public function show(string $id): Model
    {
        $query = $this->getModelQuery()
            ->where('id', $id);

        $this->showBeforeQueryExecHook($query);

        $model = $query->firstOrFail();

        $this->showAfterQueryExecHook($model);

        return $model;
    }

    protected function updateDataHook(array &$data): void
    {
    }

    protected function updateBeforeFindHook(Builder &$query): void
    {
    }

    protected function updateBeforeSaveHook(Model &$model, array $data): void
    {
    }

    protected function updateAfterSaveHook(Model &$model, array &$data): void
    {
    }

    public function update(string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $this->updateDataHook($data);

            $query = $this->getModelQuery()
                ->where('id', $id);

            $this->updateBeforeFindHook($query);

            $model = $query->firstOrFail();

            $model->fill($data);

            $this->updateBeforeSaveHook($model, $data);

            $model->save();

            $this->updateAfterSaveHook($model, $data);

            $this->storeOrUpdateRelationData($model, $data);

            $this->processFiles($model, $data);

            return $model;
        });
    }


    protected function destroyBeforeFindHook(Builder &$query): void
    {
    }

    protected function destroyBeforeDeleteHook(Model &$model): void
    {
    }

    protected function destroyAfterDeleteHook(Model &$model): void
    {
    }

    public function destroy(string $id): Model
    {
        return DB::transaction(function () use ($id) {
            $query = $this->getModelQuery()
                ->where('id', $id);

            $this->destroyBeforeFindHook($query);

            $model = $query->firstOrFail();

            $this->destroyBeforeDeleteHook($model);

            $model->delete();

            $this->destroyAfterDeleteHook($model);

            return $model;
        });
    }
}
