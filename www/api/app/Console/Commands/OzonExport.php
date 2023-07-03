<?php

namespace App\Console\Commands;

use App\Services\OzonExport\OzonExportService;
use Illuminate\Console\Command;

class OzonExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export products to ozon';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var OzonExportService $ozonExportService */
        $ozonExportService = app(OzonExportService::class);

        $ozonExportService->exportProducts();
    }
}
