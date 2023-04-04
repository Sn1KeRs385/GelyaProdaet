<?php

namespace app\Exceptions\Models\ProductItem;

use App\Exceptions\Models\AbstractModelException;
use Illuminate\Http\Response;

class CanNotChangePriceSellException extends AbstractModelException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_CHANGE_PRICE_CELL_PRODUCT_ITEM';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_change_price_sell_product_item');

        parent::__construct();
    }
}
