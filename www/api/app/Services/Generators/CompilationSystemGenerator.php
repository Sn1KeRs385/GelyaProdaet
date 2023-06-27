<?php

namespace App\Services\Generators;

use App\Enums\CompilationType;
use App\Enums\OptionGroupSlug;
use App\Models\Compilation;
use App\Models\CompilationListOption;
use App\Models\ListOption;
use App\Models\Product;
use App\Utils\WordDeclension;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompilationSystemGenerator
{
    public function __construct(protected WordDeclension $wordDeclension)
    {
    }

    /**
     * @param  ListOption[]  $listOptions
     * @return Compilation
     */
    public function generateFromListOptions(array $listOptions): Compilation
    {
        $countQuery = 'select count(*) '
            . 'from compilation_list_option '
            . 'where compilation_list_option.compilation_id = compilations.id';
        $compilation = Compilation::query()
            ->whereRaw(
                "($countQuery) = ?",
                [count($listOptions)]
            );
        foreach ($listOptions as $index => $listOption) {
            $aliasName = "clo_$index";
            $compilation->join(
                "compilation_list_option as {$aliasName}",
                function (JoinClause $query) use ($aliasName, $listOption) {
                    $query->on("{$aliasName}.compilation_id", 'compilations.id')
                        ->on("$aliasName.list_option_id", DB::raw($listOption->id));
                }
            );
        }

        $compilation = $compilation->first();

//        $compilation = Compilation::query()
//            ->whereHas('listOptions', function (Builder $query) use ($listOptions) {
//                $query->whereIn('id', Arr::pluck($listOptions, 'id'));
//            })
//            ->withCount([
//                'listOptions' => function (Builder $query) use ($listOptions) {
//                    $query->whereIn('id', Arr::pluck($listOptions, 'id'));
//                },
//            ])
//            ->get();
//
//        $compilation = $compilation->where('list_options_count', count($listOptions))->all();

        if (!$compilation) {
            $name = [];

            foreach ($listOptions as $index => $listOption) {
                $nameTemp = $listOption->title;

                if (
                    $index > 0 &&
                    in_array(
                        $listOption->group_slug,
                        Arr::pluck(OptionGroupSlug::getCategorySmallFirstLetter(), 'value')
                    )
                ) {
                    $nameTemp = mb_strtolower($nameTemp);
                }

                $name[] = $nameTemp;

                if ($listOption->group_slug === OptionGroupSlug::SIZE->value) {
                    $name[] = 'размер';
                }

                if ($listOption->group_slug === OptionGroupSlug::SIZE_YEAR->value) {
                    $year = explode('-', $listOption->title);
                    $name[] = $this->wordDeclension->afterNumDeclension(
                        $year[count($year) - 1],
                        ['год', 'года', 'лет'],
                        false
                    );
                }
            }

            $compilation = Compilation::create([
                'type' => CompilationType::SYSTEM,
                'name' => implode(' ', $name),
            ]);

            foreach ($listOptions as $listOption) {
                CompilationListOption::create([
                    'compilation_id' => $compilation->id,
                    'list_option_id' => $listOption->id,
                ]);
            }
        }

        return $compilation;
    }

    public function generateFromProduct(Product $product): void
    {
    }
}
