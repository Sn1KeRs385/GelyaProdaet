<?php

namespace App\Console\Commands;

use App\Services\OzonAttributeBindingService;
use Illuminate\Console\Command;

class OzonBindAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:bind-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bind list options to ozon attribute values';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var OzonAttributeBindingService $ozonAttributeBindingService */
        $ozonAttributeBindingService = app(OzonAttributeBindingService::class);

        $ozonAttributeBindingService->bindAttributesToListOption();
    }
}
