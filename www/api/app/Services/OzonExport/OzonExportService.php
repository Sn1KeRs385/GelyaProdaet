<?php

namespace App\Services\OzonExport;

use App\Models\OzonImportTask;
use App\Models\OzonImportTaskResult;
use App\Models\OzonProduct;
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

        OzonProduct::query()
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
            ->where(function (Builder $query) use ($lastTask) {
                $query->where(function (Builder $query) {
                    $isSoldQuery = 'SELECT pi.id FROM product_items as pi where '
                        . 'pi.product_id is not distinct from ozon_products.product_id '
                        . 'and pi.color_id is not distinct from ozon_products.color_id '
                        . 'and pi.size_id is not distinct from ozon_products.size_id '
                        . 'and pi.size_year_id is not distinct from ozon_products.size_year_id '
                        . 'and pi.count is not distinct from ozon_products.count '
                        . 'and pi.is_sold = false and pi.is_for_sale = true and pi.is_reserved = false';

                    $query->whereNull('external_id')
                        ->whereRaw("exists($isSoldQuery)");
                });
                $query->when($lastTask, function (Builder $query) use ($lastTask) {
                    $isUpdatedQuery = 'SELECT pi.id FROM product_items as pi where '
                        . 'pi.product_id is not distinct from ozon_products.product_id '
                        . 'and pi.color_id is not distinct from ozon_products.color_id '
                        . 'and pi.size_id is not distinct from ozon_products.size_id '
                        . 'and pi.size_year_id is not distinct from ozon_products.size_year_id '
                        . 'and pi.count is not distinct from ozon_products.count '
                        . 'and pi.updated_at >= ?';

                    $query->whereHas('product', function (Builder $query) use ($lastTask) {
                        $query->where('updated_at', '>=', $lastTask->created_at);
                    })
                        ->orWhereRaw("exists($isUpdatedQuery)", [$lastTask->created_at]);
                });
            })
            ->chunk(500, function (Collection $ozonProducts) use (&$ozonExportChunk) {
                foreach ($ozonProducts as $ozonProduct) {
                    if (!$ozonExportChunk->addProductItem($ozonProduct)) {
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

            $taskResult->external_product_id = $item->product_id;
            $taskResult->status = $item->status;
            $taskResult->errors = $item->errors;
            $taskResult->save();

            if ($taskResult->ozon_product_id) {
                $ozonProduct = OzonProduct::find($taskResult->ozon_product_id);
                if ($ozonProduct) {
                    $ozonProduct->external_id = $taskResult->external_product_id;
                    $ozonProduct->save();
                }
            }
        }
        if ($finishCount === $response->result->items->count()) {
            $task->is_completed = true;
            $task->save();
        }
    }
}
