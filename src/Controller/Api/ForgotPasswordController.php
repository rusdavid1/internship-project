<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\InvalidFromFieldException;
use App\Form\EmailFormProcessor;
use App\Form\ForgotPasswordFormType;
use App\Form\PasswordFormProcessor;
use App\Form\ResetPasswordFormType;
use App\Token\ResetPasswordToken;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;

class ForgotPasswordController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EmailFormProcessor $emailFormProcessor;

    private PasswordFormProcessor $passwordFormProcessor;

    private ResetPasswordToken $resetPasswordToken;

    private FormFactoryInterface $formFactory;

    private Environment $twig;

    public function __construct(
        EmailFormProcessor $emailFormProcessor,
        PasswordFormProcessor $passwordFormProcessor,
        ResetPasswordToken $resetPasswordToken,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->emailFormProcessor = $emailFormProcessor;
        $this->passwordFormProcessor = $passwordFormProcessor;
        $this->resetPasswordToken = $resetPasswordToken;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @Route(path="/api/users/forgot-password", methods={"GET", "POST"}, name="forgot_password")
     */
    public function forgotPasswordAction(Request $request): Response
    {
        $form = $this->formFactory->create(ForgotPasswordFormType::class);
        try {
            $this->emailFormProcessor->processEmailForm($form, $request);
        } catch (InvalidFromFieldException $e) {
            echo $e->getMessage();
        }

        return new Response($this->twig->render('ResetPassword/forgotPassword.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * @Route(path="/users/reset-password", methods={"GET", "POST"}, name="reset_password")
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

        $form = $this->formFactory->create(ResetPasswordFormType::class);

        try {
            $this->passwordFormProcessor->processPasswordForm($form, $request, $forgottenUser);
        } catch (InvalidFromFieldException $e) {
            echo $e->getMessage();
        }

        return new Response($this->twig->render('ResetPassword/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
