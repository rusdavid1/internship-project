<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ForgotPasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    private FormFactoryInterface $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory
    ) {
        $this->formFactory = $formFactory;
    }

    /**
     * @Route(path="/users/forgotPassword")
     */
    public function test(Request $request)
    {
        $form = $this->formFactory->create(ForgotPasswordForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();

            return $this->redirectToRoute('task_success');
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route (path="/users/redirect", name="task_success")
     */
    public function succes()
    {
        return new Response('Success', Response::HTTP_OK);
    }

}