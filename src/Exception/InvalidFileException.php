<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidFileException extends \Exception
{
    protected $message = 'The file is empty or its path is invalid';
}
