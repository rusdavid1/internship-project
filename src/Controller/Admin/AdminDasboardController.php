<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDasboardController extends AbstractController
{
    /**
     * @Route (path="/admin", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/adminDashboard.html.twig', []);
    }
}
