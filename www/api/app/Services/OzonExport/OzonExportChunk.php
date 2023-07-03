<?php

namespace App\Services\OzonExport;

use App\Enums\OzonAttributeBindingType;
use App\Exceptions\OzonRequiredAttributeMissingException;
use App\Models\File;
use App\Models\OzonAttributeBinding;
use App\Models\OzonImportTask;
use App\Models\OzonImportTaskResult;
use App\Models\ProductItem;
use App\Services\OzonDataService;
use App\Utils\SizeConverter;
use Illuminate\Support\Collection;
use Modules\Ozon\Dto\Attribute;
use Modules\Ozon\Services\OzonApiService;

class OzonExportChunk
{
    protected OzonApiService $ozonApiService;
    protected OzonDataService $ozonDataService;
    protected SizeConverter $sizeConverter;

    protected array $items = [];

    public function __construct()
    {
        $this->ozonApiService = app(OzonApiService::class);
        $this->ozonDataService = app(OzonDataService::class);
        $this->sizeConverter = app(SizeConverter::class);
    }

    public function addProductItem(ProductItem $productItem): bool
    {
        if (count($this->items) >= 100) {
            return false;
        }

        $this->handleProductItem($productItem);

        return true;
    }

    protected function handleProductItem(ProductItem $productItem): void
    {
        $categoryId = $productItem->product->ozonData->category_id;
        $price = round($productItem->priceNormalize * 3) - 1;

        $item = [
            'originalId' => $productItem->id,
            'attributes' => $this->getAttributes($productItem, $categoryId),
            'category_id' => $categoryId,
            'dimension_unit' => 'mm',
            'weight_unit' => 'g',
            'vat' => '0',
            'depth' => $productItem->product->ozonData->dept,
            'height' => $productItem->product->ozonData->height,
            'width' => $productItem->product->ozonData->width,
            'weight' => $productItem->product->ozonData->weight,
            'old_price' => (string)$price,
            'price' => (string)$price,
            'offer_id' => (string)$productItem->id,
            'name' => $this->getName($productItem),
            'images' => $this->getImageUrls($productItem),
        ];

        $this->items[] = $item;
    }

    protected function getImageUrls(ProductItem $productItem): array
    {
        return $productItem->product->files->map(fn(File $file) => $file->permanentUrl)->toArray();
    }

    protected function getName(ProductItem $productItem): string
    {
        $name = $productItem->product->type->title;

        if ($productItem->product->brand) {
            $name .= ' ' . mb_strtolower($productItem->product->brand->title);
        }

        if ($productItem->color) {
            $name .= ' ' . mb_strtolower($productItem->color->title);
        }

        return $name;
    }

    protected function getFullName(ProductItem $productItem): string
    {
        $name = $productItem->product->type->title;


        if ($productItem->product->gender) {
            $name .= ' ' . mb_strtolower($productItem->product->gender->title);
        }

        if ($productItem->product->brand) {
            $name .= ' ' . mb_strtolower($productItem->product->brand->title);
        }

        if ($productItem->product->country) {
            $name .= ' ' . mb_strtolower($productItem->product->country->title);
        }

        if ($productItem->color) {
            $name .= ' ' . mb_strtolower($productItem->color->title);
        }

        if ($productItem->size) {
            $name .= ' ' . mb_strtolower($productItem->size->title);
        }

        if ($productItem->sizeYear) {
            $size = $this->sizeConverter
                ->getSizeFromYear(
                    preg_replace('[^0-9-]', '', $productItem->sizeYear->title)
                );
            $name .= ' ' . mb_strtolower($size);
        }

        return $name;
    }

    protected function getUnionId(ProductItem $productItem): string
    {
        return "Product_{$productItem->product->id}";
    }

    protected function getAttributes(ProductItem $productItem, int $categoryId): array
    {
        $optionIds = [
            $productItem->color_id,
            $productItem->size_id,
            $productItem->size_year_id,
            $productItem->product->type_id,
            $productItem->product->brand_id,
            $productItem->product->country_id,
            $productItem->product->gender_id,
        ];
        $resultAttributes = [];

        $attributes = $this->ozonDataService->getAttributesByCategoryId($categoryId);
        $attributeIds = $attributes->map(fn(Attribute $attribute) => $attribute->id);

        /** @var Collection<int, OzonAttributeBinding>|OzonAttributeBinding[] $attributeBindings */
        $attributeBindings = OzonAttributeBinding::query()
            ->whereIn('attribute_id', $attributeIds)
            ->whereIn('list_option_id', $optionIds)
            ->get();

        foreach ($attributes as $attribute) {
            $tempAttribute = [
                'id' => $attribute->id,
                'values' => [],
            ];

//            if ($attribute->id === 31 && !$productItem->product->brand_id) {
//                $config = config('ozon.attribute_bindings')[31];
//                $tempAttribute['values'][] = [
//                    'dictionary_value_id' => $config['fallback_id'],
//                    'value' => $config['fallback_value'],
//                ];
//            }

            $findBindings = $attributeBindings->where('attribute_id', $attribute->id)->values();
            if ($findBindings->count() > 0) {
                foreach ($findBindings as $findBinding) {
                    /** @var OzonAttributeBinding $findBinding */
                    $tempAttribute['values'][] = [
                        'dictionary_value_id' => $findBinding->dictionary_value_id,
                        'value' => $findBinding->dictionary_value,
                    ];
                }
            }

            $attributes = $productItem->product->ozonData->attributes;
            if (isset($attributes[$attribute->id])) {
                $values = $attributes[$attribute->id];
                if (!is_array($values)) {
                    $values = [$values];
                }

                foreach ($values as $value) {
                    $data = explode('|', $value);
                    if (count($data) === 2) {
                        $tempAttribute['values'][] = [
                            'dictionary_value_id' => $data[0],
                            'value' => $data[1],
                        ];
                    } else {
                        $tempAttribute['values'][] = [
                            'value' => $data[0],
                        ];
                    }
                }
            }
            if (isset(config('ozon.attribute_bindings')[$attribute->id])) {
                $config = config('ozon.attribute_bindings')[$attribute->id];

                if ($config['type'] === OzonAttributeBindingType::LIST_OPTIONS) {
                    if (isset($config['fallback_id']) && count($tempAttribute['values']) === 0) {
                        $tempAttribute['values'][] = [
                            'dictionary_value_id' => $config['fallback_id'],
                            'value' => $config['fallback_value'],
                        ];
                    }
                } elseif ($config['type'] === OzonAttributeBindingType::UNION_ID) {
                    $tempAttribute['values'][] = [
                        'value' => $this->getUnionId($productItem),
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::NAME) {
                    $tempAttribute['values'][] = [
                        'value' => $this->getFullName($productItem),
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::CONSTANT) {
                    $tempAttribute['values'][] = [
                        'dictionary_value_id' => $config['value_id'],
                        'value' => $config['value'],
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::PRODUCT_ITEM_COUNT) {
                    if ($productItem->count > 0) {
                        $tempAttribute['values'][] = [
                            'value' => (string)$productItem->count,
                        ];
                    }
                }
            }

            if (count($tempAttribute['values']) > 0) {
                $resultAttributes[] = $tempAttribute;
            }
//            } elseif($attribute->is_required) {
//                throw new OzonRequiredAttributeMissingException();
//            }
        }

        return $resultAttributes;
    }

    public function startExport(): void
    {
        if (count($this->items) === 0) {
            return;
        }

        $result = $this->ozonApiService->importProducts($this->items);

        $task = OzonImportTask::create(['task_id' => $result->result->task_id]);

        foreach ($this->items as $item) {
            OzonImportTaskResult::create([
                'import_task_id' => $task->id,
                'product_item_id' => $item['originalId'],
                'offer_id' => $item['offer_id'],
            ]);
        }
    }
}
