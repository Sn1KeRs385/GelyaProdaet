<?php

namespace App\Console\Commands;

use App\Services\OzonExport\OzonExportService;
use Illuminate\Console\Command;

class OzonExportsCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:exports-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all export task';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var OzonExportService $ozonExportService */
        $ozonExportService = app(OzonExportService::class);

        $ozonExportService->checkExports();
    }
}
