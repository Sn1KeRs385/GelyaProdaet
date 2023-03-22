<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class CanNotMarkSoldProductItemException extends AbstractApiException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_MARK_SOLD_PRODUCT_ITEM';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_mark_sold_product_item');

        parent::__construct();
    }
}
