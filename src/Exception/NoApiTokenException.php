<?php

declare(strict_types=1);

namespace App\Exception;

class NoApiTokenException extends \Exception
{
    protected $message = 'No corresponding API token found !';
}
