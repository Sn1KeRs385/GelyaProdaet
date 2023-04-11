<?php

namespace app\Exceptions\Models\ProductItem;

use App\Exceptions\Models\AbstractModelException;
use Illuminate\Http\Response;

class CanNotSwitchReserveException extends AbstractModelException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_SWITCH_RESERVE';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_switch_reserve');

        parent::__construct();
    }
}
