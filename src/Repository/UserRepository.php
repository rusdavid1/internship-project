<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Programme;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ManagerRegistry $registry,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
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

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function setUserResetToken(string $emailAddress, Uuid $resetToken): void
    {
        $forgottenUser = $this->findOneBy(['email' => $emailAddress]);

        if (null !== $forgottenUser) {
            $forgottenUser->setResetToken($resetToken);
            $forgottenUser->setResetTokenCreatedAt(new \DateTime('now'));

            $this->_em->persist($forgottenUser);
            $this->_em->flush();
        }
    }

    public function changePassword(User $forgottenUser, string $plainPassword): void
    {
        $password = $this->passwordHasher->hashPassword($forgottenUser, $plainPassword);
        $forgottenUser->setPassword($password);

        $this->_em->persist($forgottenUser);
        $this->_em->flush();
    }

    public function pagination(string $page): array
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb
            ->select('u')
            ->from('App:User', 'u')
            ->orderBy('u.firstName')
            ->setFirstResult(((int)$page * 10) - 10)
            ->setMaxResults(10)
            ->getQuery();

        return $query->execute();
    }

    public function joinAProgramme(int $userId, Programme $programme): void
    {
        $user = $this->findOneBy(['id' => $userId]);

        if (null === $user) {
            throw new EntityNotFoundException();
        }

        $programme->addCustomer($user);

        $this->_em->persist($programme);
        $this->_em->flush();
    }
}
