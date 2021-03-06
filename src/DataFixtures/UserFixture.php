<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private array $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_TRAINER'];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setPassword($this->passwordHasher->hashPassword($user, 'abcds@Aaa'));
            $user->email = "abcdslk$i@email.com";
            $user->setRoles([$this->roles[$i]]);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
