<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;

        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function setUserResetToken(string $emailAddress, Uuid $resetToken)
    {
        $forgottenUser = $this->findOneBy(['email' => $emailAddress]);

        if (null !== $forgottenUser) {
            $forgottenUser->setResetToken($resetToken);
            $forgottenUser->setResetTokenCreatedAt(new \DateTime('now'));

            $this->_em->persist($forgottenUser);
            $this->_em->flush();
        }
//        TODO In case there is no user
    }

    public function validatingResetToken(Uuid $resetToken)
    {
        $forgottenUser = $this->findOneBy(['resetToken' => $resetToken]);
        $userResetToken = $forgottenUser->getResetToken();

        if ($userResetToken->compare($resetToken)) {
            return new Response('error', Response::HTTP_NOT_FOUND);
        }

        $testTimestamp = $forgottenUser->getResetTokenCreatedAt()->getTimestamp();
        $expiredTimestamp = $testTimestamp + 900;
        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();

        if ($nowTimestamp > $expiredTimestamp) {
            return new Response('Link expired', Response::HTTP_NOT_FOUND); //another function for these returns
        }

        return $forgottenUser;
    }

    public function setNewPassword(User $forgottenUser, string $plainPassword)
    {
        $password = $this->passwordHasher->hashPassword($forgottenUser, $plainPassword);
        $forgottenUser->setPassword($password);
        $forgottenUser->plainPassword = $plainPassword;

        $errors = $this->validator->validate($forgottenUser);
        if (count($errors) > 0) {
            return $errors;
        }

        $this->_em->persist($forgottenUser);
        $this->_em->flush();
    }
}
