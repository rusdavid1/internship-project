<?php

declare(strict_types=1);

namespace App\Command\Exception;

use Throwable;

class InvalidDateException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
