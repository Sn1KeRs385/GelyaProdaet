<?php

namespace App\Enums;


enum OzonAttributeBindingType: string
{
    case LIST_OPTIONS = 'LIST_OPTIONS';
    case UNION_ID = 'UNION_ID';
    case NAME = 'NAME';
    case EXCLUDE = 'EXCLUDE';
    case CONSTANT = 'CONSTANT';
    case PRODUCT_ITEM_COUNT = 'PRODUCT_ITEM_COUNT';
}
