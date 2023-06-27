<?php

namespace App\Services\Generators;

use Anper\Iuliia\Iuliia;
use App\Enums\OptionGroupSlug;
use App\Models\Compilation;
use App\Models\ListOption;
use App\Models\Product;
use App\Models\SitePage;
use App\Utils\WordDeclension;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SitePageGenerator
{
    public function __construct(
        protected CompilationSystemGenerator $compilationSystemGenerator,
        protected WordDeclension $wordDeclension
    ) {
    }

    public function generateCategories(): void
    {
        ini_set('memory_limit', '2G');

        $listOptionsQuery = ListOption::query()
            ->where('is_hidden_from_user_filters', false)
            ->whereIn('group_slug', [
                ...OptionGroupSlug::getCategoryPriority(),
                ...OptionGroupSlug::getCategorySizes()
            ]);

        foreach (OptionGroupSlug::getCategoryPriority() as $slug) {
            $listOptionsQuery->orderByRaw("group_slug = ? desc", [$slug]);
        }

        foreach (OptionGroupSlug::getCategorySizes() as $slug) {
            $listOptionsQuery->orderByRaw("group_slug = ? desc", [$slug]);
        }

        $listOptionsQuery
            ->orderBy('weight', 'desc')
            ->orderBy('title')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        $this->handleCategoriesListOptions($listOptionsQuery->get());
    }

    /**
     * @param  Collection<int, ListOption>  $listOptions
     * @param  ListOption[]  $additionalListOptions
     * @param  SitePage|null  $parentPage
     * @return void
     */
    protected function handleCategoriesListOptions(
        Collection $listOptions,
        array $additionalListOptions = [],
        ?SitePage $parentPage = null
    ): void {
        foreach ($listOptions as $listOption) {
            /** @var ListOption $listOption */
            $optionsForGenerate = [...$additionalListOptions, $listOption];
            if (in_array($listOption->group_slug, Arr::pluck(OptionGroupSlug::getCategorySizes(), 'value'))) {
                $optionsFiltered = collect();
            } else {
                $optionsFiltered = (clone $listOptions);
            }

            foreach (OptionGroupSlug::getCategoryPriority() as $slug) {
                $optionsFiltered = $optionsFiltered->where('group_slug', '<>', $slug->value);
                if ($listOption->group_slug === $slug->value) {
                    break;
                }
            }

            $optionsFiltered = $optionsFiltered->values();

            $compilation = $this->compilationSystemGenerator->generateFromListOptions($optionsForGenerate);

            $page = $this->generateFromCompilation($optionsForGenerate, $compilation, $parentPage);

            if ($optionsFiltered->count() > 0) {
                $this->handleCategoriesListOptions(
                    $optionsFiltered,
                    $optionsForGenerate,
                    $page,
                );
            }
        }
    }

    protected function generateFromCompilation(
        array $listOptions,
        Compilation $compilation,
        ?SitePage $parentPage = null
    ): SitePage {
        $sitePage = SitePage::query()
            ->where('owner_type', $compilation->getMorphClass())
            ->where('owner_id', $compilation->id)
            ->first();

        if ($sitePage) {
            return $sitePage;
        }

        $url = [''];

        foreach ($listOptions as $listOption) {
            /** @var ListOption $listOption */
            $title = $listOption->title;

            if ($listOption->group_slug === OptionGroupSlug::SIZE->value) {
                $title .= ' размер';
            }

            if ($listOption->group_slug === OptionGroupSlug::SIZE_YEAR->value) {
                $year = explode('-', $listOption->title);
                $title .= ' '
                    . $this->wordDeclension->afterNumDeclension(
                        $year[count($year) - 1],
                        ['год', 'года', 'лет'],
                        false
                    );
            }

            $url[] = Str::slug(Iuliia::translate($title, Iuliia::WIKIPEDIA));
        }

        return SitePage::create([
            'parent_id' => $parentPage?->id,
            'url' => implode('/', $url),
            'owner_type' => $compilation->getMorphClass(),
            'owner_id' => $compilation->id,
        ]);
    }

    public function generateProducts(): void
    {
        ini_set('memory_limit', '2G');

        Product::query()
            ->whereDoesntHave('sitePages')
            ->chunk(500, function (Collection $products) {
                $products->each(function (Product $product) {
                    $this->generateFromProduct($product);
                });
            });
    }

    protected function generateFromProduct(Product $product): SitePage
    {
        $listOptionsQuery = ListOption::query()
            ->where('is_hidden_from_user_filters', false)
            ->whereIn('group_slug', OptionGroupSlug::getCategoryPriority())
            ->whereIn(
                'id',
                array_filter([
                    $product->type_id,
                    $product->gender_id,
                    $product->brand_id,
                    $product->country_id
                ])
            );

        foreach (OptionGroupSlug::getCategoryPriority() as $slug) {
            $listOptionsQuery->orderByRaw("group_slug = ? desc", [$slug]);
        }

        $listOptionsQuery
            ->orderBy('weight', 'desc')
            ->orderBy('title')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
        $listOptions = $listOptionsQuery->get();

        $this->handleCategoriesListOptions($listOptionsQuery->get());

        $compilation = $this->compilationSystemGenerator->generateFromListOptions($listOptions->all());

        $page = $this->generateFromCompilation($listOptions->all(), $compilation);

        return SitePage::create([
            'parent_id' => $page->id,
            'url' => implode('/', [$page->url, $product->id]),
            'owner_type' => $product->getMorphClass(),
            'owner_id' => $product->id,
        ]);
    }
}
