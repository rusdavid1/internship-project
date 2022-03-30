<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ForgotPasswordController extends AbstractController
{
    private FormFactoryInterface $formFactory;

    private EntityManagerInterface $entityManager;

    private MailerInterface $mailer;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route(path="/users/forgotPassword")
     */
    public function test(Request $request)
    {
        $form = $this->formFactory->create(ForgotPasswordForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emailAddress = $form->getData()['email'];
            $userRepo = $this->entityManager->getRepository(User::class);
            $forgottenUser = $userRepo->findOneBy(['email' => $emailAddress]);

            if (null !== $forgottenUser) { //more explicit
                $resetToken = Uuid::v4();
                $forgottenUser->setResetToken($resetToken);
                $forgottenUser->setResetTokenCreatedAt(new \DateTime('now'));
                $this->entityManager->persist($forgottenUser);
                $this->entityManager->flush();

                $resetPasswordUrl = "http://internship.local/users/resetPassword?resetToken=$resetToken";

                $email = (new Email())
                    ->from('rusdavid99@gmail.com')
                    ->to($emailAddress)
                    ->subject('Password Reset')
                    ->text("Someone tried to reset you account's password. If you did access this link:")
                    ->html("<a href=$resetPasswordUrl>Reset password</a>");


                $this->mailer->send($email);
            }
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/users/resetPassword")
     */
    public function resetPassword(Request $request)
    {
        $queries = $request->query->all();
        $resetToken = $queries['resetToken'];

        $userRepo = $this->entityManager->getRepository(User::class);
        $forgottenUser = $userRepo->findOneBy(['resetToken' => $resetToken]);

//        if ($forgottenUser->getResetToken() !== $resetToken) { //how to compare
//            return new Response('error', Response::HTTP_NOT_FOUND);
//        }

//        need to check for token expiration
        $testTimestamp = $forgottenUser->getResetTokenCreatedAt()->getTimestamp();
        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();
        var_dump($testTimestamp);

//        if ($testTimestamp * 900 > $nowTimestamp) {
//            return new Response('')
//        }

//        new password form

//        persist password to db

        return new Response('hello');
    }

    /**
     * @Route (path="/users/redirect", name="task_success")
     */
    public function succes()
    {
        return new Response('Success', Response::HTTP_OK);
    }

}