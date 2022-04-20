<?php

declare(strict_types=1);

namespace App\Exception;

class NoEmptyRoomsLeftException extends \Exception
{
    protected $message = 'No more empty rooms left';
}
