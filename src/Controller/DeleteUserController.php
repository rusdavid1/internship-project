<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ProcessRecoverAccountForm;
use App\Form\RecoverAccountFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/api/users")
 */
class DeleteUserController extends AbstractController
{
    private UserRepository $userRepository;

    private ProcessRecoverAccountForm $recoverAccountForm;

    public function __construct(
        ProcessRecoverAccountForm $recoverAccountForm,
        UserRepository $userRepository
    ) {
        $this->recoverAccountForm = $recoverAccountForm;
        $this->userRepository = $userRepository;
    }


    /**
     * @Route (methods={"DELETE"})
     */
    public function deleteUserAction(Request $request): Response
    {
        $id = $request->query->get('id');

        return $this->userRepository->softDeleteUser($id);
    }

    /**
     * @Route (path="/recover", methods={"POST"})
     */
    public function recoverUserAction(Request $request): Response
    {
        $form = $this->createForm(RecoverAccountFormType::class);

        $email = $this->recoverAccountForm->getEmail($form, $request);
        $this->userRepository->recoverSoftDeletedUser($email);

        return $this->renderForm('recovery/recoverAccount.html.twig', [
            'form' => $form,
        ]);
    }
}
