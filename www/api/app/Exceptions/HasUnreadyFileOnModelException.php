<?php

namespace App\Exceptions;

use Throwable;

class HasUnreadyFileOnModelException extends AbstractJobException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(__('exceptions.has_unready_file_on_model'), $code, $previous);
    }
}
