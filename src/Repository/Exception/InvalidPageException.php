<?php

declare(strict_types=1);

namespace App\Repository\Exception;

class InvalidPageException extends \Exception
{
    protected $message = 'Invalid page. It must be at least page 1';
}
