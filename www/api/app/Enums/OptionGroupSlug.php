<?php

namespace App\Enums;


enum OptionGroupSlug: string
{
    case BRAND = 'brand';
    case COUNTRY = 'country';
    case COLOR = 'color';
    case SIZE = 'size';
    case SIZE_YEAR = 'size_year';
    case PRODUCT_TYPE = 'product_type';
    case GENDER = 'gender';
}
