<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/animals", name="animals", methods={"GET"})
     */
    public function getAnimal(Request $request): Response
    {
        $customer = new User();
        $customer->setRoles(new ArrayCollection(['cusomer', 'user']));

        $userRepo = $this->entityManager->getRepository(User::class);

        $programme = new Programme();
        $programme->setStartDate(new \DateTime('now'));
        $programme->setEndDate(new \DateTime('tomorrow'));

//        $programme->setCustomers(new ArrayCollection([$customer]));

        $customer = $userRepo->find(1);
//        $this->entityManager->persist($customer);
        $this->entityManager->persist($programme);

        $customer->addProgramme($programme);

        var_dump(count($customer->getProgrammes()));
        var_dump(count($programme->getCustomers()));

        $this->entityManager->flush();

        return new Response('Wooof wooof, im a dog', RESPONSE::HTTP_OK, []);
    }
}

