<?php

declare(strict_types=1);

namespace App\Exception;

class FileNotFoundException extends \Exception
{
    protected $message = 'File not found';
}
