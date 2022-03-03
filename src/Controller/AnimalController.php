<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController extends AbstractController
{
    /**
     @Route("/animals")
     */
    public function getAnimal(Request $request): Response
    {
        return new Response('Wooof wooof, im a dog', RESPONSE::HTTP_OK, []);

    }
}