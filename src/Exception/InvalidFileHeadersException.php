<?php

declare(strict_types=1);

namespace App\Command\Exception;

class InvalidFileHeadersException extends \Exception
{
    protected $message = 'The file does not have headers. They are necessary';
}
