<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/api/programmes")
 */
class ProgrammeController
{
    private ProgrammeRepository $programmeRepository;

    public function __construct(ProgrammeRepository $programmeRepository)
    {
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @Route (methods={"GET"}, name="api_get_programmes")
     */
    public function getAllProgrammes(): array
    {
        return $this->programmeRepository->findAll();
    }
}
