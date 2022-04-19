<?php

declare(strict_types=1);

namespace App\Repository\Exception;

class ProgrammeNameNotFoundException extends \Exception
{
    protected $message = 'The programme you searched for doesn\'t exist yet';
}
