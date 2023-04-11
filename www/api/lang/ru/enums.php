<?php

use App\Enums\OptionGroupSlug;

return [
    'option_group_slug_human' => [
        OptionGroupSlug::BRAND->value => 'Бренд',
        OptionGroupSlug::COUNTRY->value => 'Страна',
        OptionGroupSlug::COLOR->value => 'Цвет',
        OptionGroupSlug::SIZE->value => 'Размер',
        OptionGroupSlug::SIZE_YEAR->value => 'Возраст',
        OptionGroupSlug::PRODUCT_TYPE->value => 'Тип товара',
        OptionGroupSlug::GENDER->value => 'Пол',
    ]
];
