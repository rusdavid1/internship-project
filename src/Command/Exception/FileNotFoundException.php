<?php

declare(strict_types=1);

namespace App\Command\Exception;

use Throwable;

class FileNotFoundException extends \Exception
{
    protected $message = 'File not found';
}
