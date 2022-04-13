<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $bookedDays = $this->programmeRepository->getBookedProgrammesDays();
        $dates = [];

        foreach ($bookedDays as $programmeDay) {
            $date = [];

            $test = $this->programmeRepository->getBusiestHours($programmeDay['day']);
            $date['hour'] = $test[0]['hour'];
            $date['day'] = $programmeDay['day'];

            $dates[] = $date;
        }

        return $this->render('admin/adminDashboard.html.twig', ['dates' => $dates]);
    }
}
