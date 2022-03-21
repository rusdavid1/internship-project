<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/api/programme")
 */
class ProgrammeController
{

    /**
     * @Route (methods={GET})
     */
    public function getAllProgrammes(): Response
    {
        return new Response('Hello from programmes');
    }
}