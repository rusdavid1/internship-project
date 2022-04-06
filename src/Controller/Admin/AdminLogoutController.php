<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminLogoutController extends AbstractController
{
    /**
     * @Route(path="/admin/logout", name="admin_logout", methods={"GET"})
     */
    public function logOutAction(): void
    {
    }
}
