<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route (path="/dashboard", methods={"GET"}, name="dashboard")
     */
    public function index(): Response
    {
        $logoutUrl = $this->generateUrl('admin_logout');

        return $this->render('admin/adminDashboard.html.twig', []);
    }
}
