<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\InvalidFromFieldException;
use App\Form\EmailFormProcessor;
use App\Form\ForgotPasswordFormType;
use App\Form\PasswordFormProcessor;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Token\ResetPasswordToken;
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

    private EmailFormProcessor $emailFormProcessor;

    private PasswordFormProcessor $passwordFormProcessor;

    private ResetPasswordToken $resetPasswordToken;

    public function __construct(
        EmailFormProcessor $emailFormProcessor,
        PasswordFormProcessor $passwordFormProcessor,
        ResetPasswordToken $resetPasswordToken
    ) {
        $this->emailFormProcessor = $emailFormProcessor;
        $this->passwordFormProcessor = $passwordFormProcessor;
        $this->resetPasswordToken = $resetPasswordToken;
    }

    /**
     * @Route(path="/users/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        try {
            $this->emailFormProcessor->processEmailForm($form, $request);
        } catch (InvalidFromFieldException $e) {
            echo $e->getMessage();
        }

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

        $forgottenUser = $this->resetPasswordToken->validatingResetToken($resetToken);
        if (null === $forgottenUser) {
            $this->logger->warning('Invalid reset token');

            return new Response('Invalid token', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ResetPasswordFormType::class);

        try {
            $this->passwordFormProcessor->processPasswordForm($form, $request, $forgottenUser);
        } catch (InvalidFromFieldException $e) {
            echo $e->getMessage();
        }

        return $this->render('ResetPassword/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
