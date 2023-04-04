<?php

namespace app\Exceptions\Models\ProductItem;

use App\Exceptions\Models\AbstractModelException;
use Illuminate\Http\Response;

class CanNotMarkNotForSaleException extends AbstractModelException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_MARK_NOT_FOR_SALE_PRODUCT_ITEM';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_mark_not_for_sale_product_item');

        parent::__construct();
    }
}
