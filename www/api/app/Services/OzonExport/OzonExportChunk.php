<?php

namespace App\Services\OzonExport;

use App\Enums\OzonAttributeBindingType;
use App\Models\File;
use App\Models\OzonAttributeBinding;
use App\Models\OzonImportTask;
use App\Models\OzonImportTaskResult;
use App\Models\OzonProduct;
use App\Models\ProductItem;
use App\Services\OzonDataService;
use App\Utils\SizeConverter;
use Illuminate\Database\Eloquent\Builder;
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

    public function addProductItem(OzonProduct $product): bool
    {
        if (count($this->items) >= 100) {
            return false;
        }

        $this->handleProductItem($product);

        return true;
    }

    protected function handleProductItem(OzonProduct $product): void
    {
        $categoryId = $product->product->ozonData->category_id;
        $price = $this->getPrice($product);

        $item = [
            'originalId' => $product->id,
            'attributes' => $this->getAttributes($product, $categoryId),
            'category_id' => $categoryId,
            'dimension_unit' => 'mm',
            'weight_unit' => 'g',
            'vat' => '0',
            'depth' => $product->product->ozonData->dept,
            'height' => $product->product->ozonData->height,
            'width' => $product->product->ozonData->width,
            'weight' => $product->product->ozonData->weight,
            'old_price' => (string)$price,
            'price' => (string)$price,
            'offer_id' => (string)$product->id,
            'name' => $this->getName($product),
            'images' => $this->getImageUrls($product),
        ];

        $this->items[] = $item;
    }

    protected function getProductItemQuery(OzonProduct $product): Builder
    {
        return ProductItem::query()
            ->where('product_id', $product->product_id)
            ->where('size_id', $product->size_id)
            ->where('size_year_id', $product->size_year_id)
            ->where('color_id', $product->color_id)
            ->where('count', $product->count);
    }

    protected function getOldPrice(OzonProduct $product): int
    {
        /** @var ProductItem $productItem */
        $productItem = $this->getProductItemQuery($product)
            ->orderByDesc('price')
            ->first();

        return (int)(round($productItem->priceNormalize * 3)) - 10;
    }

    protected function getPrice(OzonProduct $product): int
    {
        /** @var ProductItem $productItem */
        $productItem = $this->getProductItemQuery($product)
            ->orderByDesc('price')
            ->first();

        return (int)(round($productItem->priceNormalize * 3)) - 10;
    }

    protected function getImageUrls(OzonProduct $product): array
    {
        return $product->product->files->map(fn(File $file) => $file->permanentUrl)->toArray();
    }

    protected function getName(OzonProduct $product): string
    {
        $name = $product->product->type->title;

        if ($product->product->brand) {
            $name .= ' ' . mb_strtolower($product->product->brand->title);
        }

        if ($product->color) {
            $name .= ' ' . mb_strtolower($product->color->title);
        }

        return $name;
    }

    protected function getFullName(OzonProduct $product): string
    {
        $name = $product->product->type->title;


        if ($product->product->gender) {
            $name .= ' ' . mb_strtolower($product->product->gender->title);
        }

        if ($product->product->brand) {
            $name .= ' ' . mb_strtolower($product->product->brand->title);
        }

        if ($product->product->country) {
            $name .= ' ' . mb_strtolower($product->product->country->title);
        }

        if ($product->color) {
            $name .= ' ' . mb_strtolower($product->color->title);
        }

        if ($product->size) {
            $name .= ' ' . mb_strtolower($product->size->title);
        }

        if ($product->sizeYear) {
            $size = $this->sizeConverter
                ->getSizeFromYear(
                    preg_replace('[^0-9-]', '', $product->sizeYear->title)
                );
            $name .= ' ' . mb_strtolower($size);
        }

        return $name;
    }

    protected function getUnionId(OzonProduct $product): string
    {
        return "Product_{$product->product->id}";
    }

    protected function getAttributes(OzonProduct $product, int $categoryId): array
    {
        $optionIds = [
            $product->color_id,
            $product->size_id,
            $product->size_year_id,
            $product->product->type_id,
            $product->product->brand_id,
            $product->product->country_id,
            $product->product->gender_id,
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

            $attributes = $product->product->ozonData->attributes;
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
                        'value' => $this->getUnionId($product),
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::NAME) {
                    $tempAttribute['values'][] = [
                        'value' => $this->getFullName($product),
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::CONSTANT) {
                    $tempAttribute['values'][] = [
                        'dictionary_value_id' => $config['value_id'],
                        'value' => $config['value'],
                    ];
                } elseif ($config['type'] === OzonAttributeBindingType::PRODUCT_ITEM_COUNT) {
                    if ($product->count > 1) {
                        $tempAttribute['values'][] = [
                            'value' => (string)$product->count,
                        ];
                    }
                }
            }

            if (count($tempAttribute['values']) > 0) {
                $resultAttributes[] = $tempAttribute;
            }
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
                'ozon_product_id' => $item['originalId'],
                'offer_id' => $item['offer_id'],
            ]);
        }
    }
}
