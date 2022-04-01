<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\EmailFormProcessor;
use App\Form\ForgotPasswordFormType;
use App\Form\PasswordFormProcessor;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private UserRepository $userRepository;

    private ForgotPasswordFormType $forgotPasswordForm;

    private ResetPasswordFormType $resetPasswordFormType;

    private EmailFormProcessor $emailFormProcessor;

    private PasswordFormProcessor $passwordFormProcessor;

    public function __construct(
        UserRepository $userRepository,
        ForgotPasswordFormType $forgotPasswordForm,
        ResetPasswordFormType $resetPasswordFormType,
        EmailFormProcessor $emailFormProcessor,
        PasswordFormProcessor $passwordFormProcessor
    ) {
        $this->userRepository = $userRepository;
        $this->forgotPasswordForm = $forgotPasswordForm;
        $this->resetPasswordFormType = $resetPasswordFormType;
        $this->emailFormProcessor = $emailFormProcessor;
        $this->passwordFormProcessor = $passwordFormProcessor;
    }

    /**
     * @Route(path="/users/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        $this->emailFormProcessor->processEmailForm($form, $request);

        return $this->render('ResetPassword/forgotPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/users/reset-password", name="reset_password")
     */
    public function resetPasswordAction(Request $request): Response
    {
        $resetToken = $request->query->all()['resetToken'];
        $resetToken = Uuid::fromString($resetToken);

        $forgottenUser = $this->userRepository->validatingResetToken($resetToken);
        if (is_string($forgottenUser)) {
            $this->logger->warning('Invalid reset token');

            return new Response($forgottenUser);
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $this->passwordFormProcessor->processPasswordForm($form, $request, $forgottenUser);

        return $this->render('ResetPassword/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
