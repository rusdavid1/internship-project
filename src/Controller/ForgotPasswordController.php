<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ForgotPasswordForm;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordController extends AbstractController
{
    private UserRepository $userRepository;

    private ForgotPasswordForm $forgotPasswordForm;

    private ResetPasswordFormType $resetPasswordFormType;

    public function __construct(
        UserRepository $userRepository,
        ForgotPasswordForm $forgotPasswordForm,
        ResetPasswordFormType $resetPasswordFormType
    ) {
        $this->userRepository = $userRepository;
        $this->forgotPasswordForm = $forgotPasswordForm;
        $this->resetPasswordFormType = $resetPasswordFormType;
    }

    /**
     * @Route(path="/users/forgot-password")
     */
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgotPasswordForm::class);
        $this->forgotPasswordForm->processEmailForm($form, $request);

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/users/reset-password")
     */
    public function resetPasswordAction(Request $request)
    {
        $queries = $request->query->all();
        $resetToken = $queries['resetToken'];
        $resetToken = Uuid::fromString($resetToken);

        $forgottenUser = $this->userRepository->validatingResetToken($resetToken);

        $form = $this->createForm(ResetPasswordFormType::class);
        $this->resetPasswordFormType->processPasswordForm($form, $request, $forgottenUser);

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
