<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ProgrammeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DashboardController
{
    private ProgrammeRepository $programmeRepository;

    private Environment $twig;

    public function __construct(ProgrammeRepository $programmeRepository, Environment $twig)
    {
        $this->programmeRepository = $programmeRepository;
        $this->twig = $twig;
    }

    /**
     * @Route (path="/dashboard", methods={"GET"}, name="dashboard")
     */
    public function index(): Response
    {
        $dates = $this->programmeRepository->getBusiestHours();

        return new Response($this->twig->render('admin/adminDashboard.html.twig', ['dates' => $dates]));
    }
}
