<?php

namespace App\Services\OzonExport;

use App\Models\OzonImportTask;
use App\Models\OzonImportTaskResult;
use App\Models\ProductItem;
use App\Services\OzonDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Modules\Ozon\Services\OzonApiService;
use Sn1KeRs385\FileUploader\App\Enums\FileStatus;

class OzonExportService
{
    public function __construct(protected OzonApiService $ozonApiService)
    {
    }

    public function exportProducts(): void
    {
        $lastTask = OzonImportTask::query()
            ->orderByDesc('id')
            ->first();

        $ozonExportChunk = new OzonExportChunk();

        ProductItem::query()
            ->whereHas('product', function (Builder $query) {
                $query->whereHas('ozonData')
                    ->whereHas('files', function (Builder $query) {
                        $query->where('status', FileStatus::FINISHED);
                    });
            })
            ->with([
                'size',
                'color',
                'sizeYear',
                'product' => function (BelongsTo $query) {
                    $query->with([
                        'type',
                        'gender',
                        'brand',
                        'country',
                        'ozonData',
                        'files' => function (MorphMany $query) {
                            $query->where('status', FileStatus::FINISHED);
                        },
                    ]);
                }
            ])
            ->when($lastTask, function (Builder $query) use ($lastTask) {
                $query->where('updated_at', '>=', $lastTask->created_at)
                    ->orWhere(function (Builder $query) {
                        $query->whereNull('ozon_product_id')
                            ->where('is_sold', false)
                            ->where('is_for_sale', true)
                            ->where('is_reserved', false);
                    });
            })
            ->when(!$lastTask, function (Builder $query) use ($lastTask) {
                $query->whereNull('ozon_product_id')
                    ->where('is_sold', false)
                    ->where('is_for_sale', true)
                    ->where('is_reserved', false);
            })
            ->chunk(500, function (Collection $productItems) use (&$ozonExportChunk) {
                foreach ($productItems as $productItem) {
                    if (!$ozonExportChunk->addProductItem($productItem)) {
                        $ozonExportChunk->startExport();
                        $ozonExportChunk = new OzonExportChunk();
                    }
                }
            });

        $ozonExportChunk->startExport();
    }

    public function checkExports(): void
    {
        OzonImportTask::query()
            ->where('is_completed', false)
            ->chunk(100, function (Collection $tasks) {
                foreach ($tasks as $task) {
                    $this->checkOzonTask($task);
                }
            });
    }

    protected function checkOzonTask(OzonImportTask $task): void
    {
        $response = $this->ozonApiService->importProductsInfo($task->task_id);

        $finishCount = 0;
        foreach ($response->result->items as $item) {
            if (in_array($item->status, ['imported', 'failed'])) {
                $finishCount += 1;
            }

            $taskResult = OzonImportTaskResult::query()
                ->where('import_task_id', $task->id)
                ->where('offer_id', $item->offer_id)
                ->first();
            if (!$taskResult) {
                continue;
            }

            $taskResult->status = $item->status;
            $taskResult->errors = $item->errors;
            $taskResult->result();

            if ($taskResult->product_id) {
                $productItem = ProductItem::find($taskResult->product_item_id);
                if ($productItem) {
                    $productItem->ozon_product_id = $taskResult->product_id;
                    $productItem->save();
                }
            }
        }
        if ($finishCount === $response->result->items->count()) {
            $task->is_completed = true;
            $task->save();
        }
    }
}
