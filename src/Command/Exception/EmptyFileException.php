<?php

declare(strict_types=1);

namespace App\Command\Exception;

class EmptyFileException extends \Exception
{
    protected $message = 'The file is empty';
}
