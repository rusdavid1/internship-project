<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route (methods={"GET"})
     */
    public function index()
    {
        return $this->render('admin/adminDashboard.html.twig', []);
    }

    /**
     * @Route (path="/login", name="admin_login")
     */
    public function loginAction()
    {
        return $this->render('login/adminLogin.html.twig', []);
    }
}