<?php

namespace App\Console\Commands;

use App\Enums\CompilationType;
use App\Models\Compilation;
use App\Models\CompilationListOption;
use App\Models\SitePage;
use App\Services\Generators\SitePageGenerator;
use Illuminate\Console\Command;

class PageGeneratorCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-generator:categories {--truncate} {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all categories pages';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->truncateTables();
        $this->clearSystem();

        /** @var SitePageGenerator $sitePageGenerator */
        $sitePageGenerator = app(SitePageGenerator::class);

        $sitePageGenerator->generateCategories();
    }

    protected function truncateTables(): void
    {
        if (!$this->option('truncate')) {
            return;
        }

        CompilationListOption::query()
            ->truncate();
        SitePage::query()
            ->truncate();
        Compilation::query()
            ->truncate();
    }

    protected function clearSystem(): void
    {
        if (!$this->option('clear')) {
            return;
        }

        $compilationIdsQuery = Compilation::query()
            ->select('id')
            ->where('type', CompilationType::SYSTEM);

        CompilationListOption::query()
            ->whereIn('compilation_id', $compilationIdsQuery)
            ->delete();

        SitePage::query()
            ->where('owner_type', (new Compilation())->getMorphClass())
            ->whereIn('owner_id', $compilationIdsQuery)
            ->delete();

        $compilationIdsQuery->delete();
    }
}
