<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidFromFieldException extends \Exception
{
    protected $message = 'The form submitted has invalid fields';
}
