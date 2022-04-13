<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private ProgrammeRepository $programmeRepository;

    public function __construct(ProgrammeRepository $programmeRepository)
    {
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @Route (path="/dashboard", methods={"GET"}, name="dashboard")
     */
    public function index(): Response
    {
        $test = $this->programmeRepository->getBusiestHours();

        return $this->render('admin/adminDashboard.html.twig', ['dates' => $test]);
    }
}
