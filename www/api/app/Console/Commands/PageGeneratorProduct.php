<?php

namespace App\Console\Commands;

use App\Enums\CompilationType;
use App\Models\Compilation;
use App\Models\CompilationListOption;
use App\Models\Product;
use App\Models\SitePage;
use App\Services\Generators\SitePageGenerator;
use Illuminate\Console\Command;

class PageGeneratorProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-generator:product {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all products pages';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->clear();

        /** @var SitePageGenerator $sitePageGenerator */
        $sitePageGenerator = app(SitePageGenerator::class);

        $sitePageGenerator->generateProducts();
    }

    protected function clear(): void
    {
        if (!$this->option('clear')) {
            return;
        }

        SitePage::query()
            ->where('owner_type', (new Product())->getMorphClass())
            ->delete();
    }
}
