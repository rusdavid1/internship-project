<?php

declare(strict_types=1);

namespace App\Command\Exception;

class FileNotFoundException extends \Exception
{
    protected $message = 'File not found';
}
