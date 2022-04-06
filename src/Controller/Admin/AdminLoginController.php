<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminLoginController extends AbstractController
{
    /**
     * @Route (path="/admin/login", name="admin_login")
     */
    public function loginAction(): Response
    {
        return $this->render('login/adminLogin.html.twig', []);
    }
}
