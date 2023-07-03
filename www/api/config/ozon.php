<?php

use App\Enums\OptionGroupSlug;
use App\Enums\OzonAttributeBindingType;

return [
    'client_id' => env('OZON_CLIENT_ID'),
    'api_key' => env('OZON_API_KEY'),

    'attribute_bindings' => [
        31 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::BRAND,
            'is_multiple' => false,
            'fallback_id' => 126745801,
            'fallback_value' => 'Нет категории',
        ],
        4295 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::SIZE,
            'is_multiple' => true,
        ],
        8292 => [
            'type' => OzonAttributeBindingType::UNION_ID,
        ],
        9163 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::GENDER,
            'is_multiple' => true,
        ],
        10096 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::COLOR,
            'is_multiple' => true,
        ],
        4180 => [
            'type' => OzonAttributeBindingType::NAME,
        ],
        4296 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::SIZE,
            'is_multiple' => true,
        ],
        4389 => [
            'type' => OzonAttributeBindingType::LIST_OPTIONS,
            'categories_id' => [22825712],
            'list_option_slug' => OptionGroupSlug::COUNTRY,
            'is_multiple' => false,
        ],
        8789 => [
            'type' => OzonAttributeBindingType::EXCLUDE,
        ],
        8790 => [
            'type' => OzonAttributeBindingType::EXCLUDE,
        ],
        9390 => [
            'type' => OzonAttributeBindingType::CONSTANT,
            'value_id' => 43242,
            'value' => 'Детская',
        ],
        9661 => [
            'type' => OzonAttributeBindingType::PRODUCT_ITEM_COUNT,
        ],
    ],

    'url' => [
        'base' => 'https://api-seller.ozon.ru',
        'category' => [
            'tree' => '/v2/category/tree',
            'attributes' => '/v3/category/attribute',
            'attribute_values' => '/v2/category/attribute/values',
        ],
        'product' => [
            'import' => '/v2/product/import',
            'info' => '/v1/product/import/info',
        ],
    ],
];
