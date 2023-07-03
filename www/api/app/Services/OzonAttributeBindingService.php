<?php

namespace App\Services;


use App\Enums\OptionGroupSlug;
use App\Enums\OzonAttributeBindingType;
use App\Models\ListOption;
use App\Models\OzonAttributeBinding;
use App\Utils\SizeConverter;
use Illuminate\Support\Collection;
use Modules\Ozon\Dto\AttributeValue;

class OzonAttributeBindingService
{
    public function __construct(protected OzonDataService $ozonDataService, protected SizeConverter $sizeConverter)
    {
    }

    public function bindAttributesToListOption(): void
    {
        $attributeBindingsConfig = config('ozon.attribute_bindings');

        foreach ($attributeBindingsConfig as $attributeId => $config) {
            if ($config['type'] !== OzonAttributeBindingType::LIST_OPTIONS) {
                continue;
            }

            $attributeValues = $this->ozonDataService->getAttributeValues($config['categories_id'][0], $attributeId);

//            file_put_contents(
//                'test.txt',
//                implode(
//                    "\n",
//                    $attributeValues->map(fn($attribute) => "$attribute->id|$attribute->value")->toArray()
//                )
//            );
//            dd(123);
            $groupSlugs = [$config['list_option_slug']];
            if ($config['list_option_slug'] === OptionGroupSlug::SIZE) {
                $groupSlugs[] = OptionGroupSlug::SIZE_YEAR;
            }
            ListOption::query()
                ->whereIn('group_slug', $groupSlugs)
                ->chunk(100, function (Collection $listOptions) use ($attributeValues, $attributeId, $config) {
                    foreach ($listOptions as $listOption) {
                        $this->bindListOption(
                            $listOption,
                            $attributeValues,
                            $attributeId,
                            $config
                        );
                    }
                });
        }
    }

    /**
     * @param  ListOption  $listOption
     * @param  Collection<int, AttributeValue>|AttributeValue[]  $attributeValues
     * @param  int  $attributeId
     * @param  array  $config
     * @return void
     */
    protected function bindListOption(
        ListOption $listOption,
        Collection $attributeValues,
        int $attributeId,
        array $config
    ): void {
        $searchStrings = $this->modifySearchString(OptionGroupSlug::from($listOption->group_slug), $listOption->title);

        $found = false;

        foreach ($attributeValues as $attributeValue) {
            $searchStringFound = false;
            foreach ($searchStrings as $searchString) {
                if (mb_strtolower($attributeValue->value) === mb_strtolower($searchString)) {
                    $searchStringFound = true;
                    break;
                }
            }
            if (!$searchStringFound) {
                continue;
            }

            $found = true;
            if ($config['is_multiple']) {
                OzonAttributeBinding::updateOrCreate([
                    'attribute_id' => $attributeId,
                    'list_option_id' => $listOption->id,
                    'dictionary_value_id' => $attributeValue->id,
                    'dictionary_value' => $attributeValue->value,
                ]);
            } else {
                OzonAttributeBinding::updateOrCreate([
                    'attribute_id' => $attributeId,
                    'list_option_id' => $listOption->id,
                ], [
                    'dictionary_value_id' => $attributeValue->id,
                    'dictionary_value' => $attributeValue->value,
                ]);
            }

            if (!$config['is_multiple']) {
                break;
            }
        }

        if (!$found && isset($config['fallback_id'])) {
            foreach ($attributeValues as $attributeValue) {
                if ($attributeValue->id !== $config['fallback_id']) {
                    continue;
                }

                OzonAttributeBinding::updateOrCreate([
                    'attribute_id' => $attributeId,
                    'list_option_id' => $listOption->id,
                ], [
                    'dictionary_value_id' => $attributeValue->id,
                    'dictionary_value' => $attributeValue->value,
                ]);
                break;
            }
        }
    }

    protected function modifySearchString(OptionGroupSlug $groupSlug, string $searchString): array
    {
        if ($groupSlug === OptionGroupSlug::BRAND && $searchString === 'H&M') {
            return ['H&M Kids'];
        }

        if ($groupSlug === OptionGroupSlug::COLOR) {
            return explode(', ', $searchString);
        }

        if ($groupSlug === OptionGroupSlug::GENDER) {
            $strings = [$searchString];
            if ($searchString === 'На девочку') {
                $strings[] = 'Девочки';
            }
            if ($searchString === 'На мальчика') {
                $strings[] = 'Мальчики';
            }
            if ($searchString === 'На мальчика и девочку') {
                $strings[] = 'Девочки';
                $strings[] = 'Мальчики';
            }
            return $strings;
        }

        if ($groupSlug === OptionGroupSlug::SIZE) {
            return explode('-', $searchString);
        }

        if ($groupSlug === OptionGroupSlug::SIZE_YEAR) {
            return $this->sizeConverter->getSizeFromYear(preg_replace('[^0-9-]', '', $searchString));
        }

        return [$searchString];
    }
}
