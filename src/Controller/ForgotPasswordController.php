<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordForm;
use App\Form\ResetPasswordFormType;
use App\Mail\ForgotPasswordMail;
use App\Repository\UserRepository;
use App\Traits\ValidatorTrait;
use App\Validator\Date;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\VarDumper\Cloner\Data;

class ForgotPasswordController extends AbstractController
{
    use ValidatorTrait;

    private EntityManagerInterface $entityManager;

    private MailerInterface $mailer;

    private UserPasswordHasherInterface $passwordHasher;

    private ValidatorInterface $validator;

    private UserRepository $userRepository;

    private ForgotPasswordMail $forgotPasswordMail;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        ForgotPasswordMail $forgotPasswordMail
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->forgotPasswordMail = $forgotPasswordMail;
    }

    /**
     * @Route(path="/users/forgot-password")
     */
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgotPasswordForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailAddress = $form->getData()['email'];

            $resetToken = Uuid::v4();
            $this->userRepository->setUserResetToken($emailAddress, $resetToken);

            $this->forgotPasswordMail->sendResetPasswordMail($emailAddress, $resetToken);
        }

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

        $userRepo = $this->entityManager->getRepository(User::class);
        $forgottenUser = $userRepo->findOneBy(['resetToken' => $resetToken]);
        $userResetToken = $forgottenUser->getResetToken();

        if ($userResetToken->compare($resetToken)) { //how to compare
            return new Response('error', Response::HTTP_NOT_FOUND);
        }

        $testTimestamp = $forgottenUser->getResetTokenCreatedAt()->getTimestamp();
        $expiredTimestamp = $testTimestamp + 900;
        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();

        if ($nowTimestamp > $expiredTimestamp) {
            return new Response('Link expired', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//        persist password to db
            $plainPassword = $form->getData()['password'];
            $password = $this->passwordHasher->hashPassword($forgottenUser, $plainPassword);
            $forgottenUser->setPassword($password);
            $forgottenUser->plainPassword = $plainPassword;

            $errors = $this->validator->validate($forgottenUser);
            if (count($errors) > 0) {
                return $this->displayErrors($errors);
            }

            $this->entityManager->persist($forgottenUser);
            $this->entityManager->flush();

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