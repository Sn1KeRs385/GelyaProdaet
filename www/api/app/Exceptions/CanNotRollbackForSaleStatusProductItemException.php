<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class CanNotRollbackForSaleStatusProductItemException extends AbstractApiException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_ROLLBACK_FOR_SALE_STATUS_PRODUCT_ITEM';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_rollback_for_sale_status_product_item');

        parent::__construct();
    }
}
