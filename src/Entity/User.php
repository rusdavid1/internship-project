<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    private const ROLE_TRAINER = 'ROLE_TRAINER';
    private const ROLE_USER = 'ROLE_USER';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $firstName = '';

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $lastName = '';

    /**
     * @ORM\Column(type="string", length=13, options={"fixed" = true})
     * @MyAssert\Cnp()
     */
    public string $cnp = '';

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    public string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    public string $password;

    /**
     * @ORM\Column(type="string")
     * @MyAssert\Password()
     */
    public string $plainPassword;

    /**
    * @ORM\Column(type="uuid", unique=true, nullable=true)
    */
    private Uuid $apiToken;

    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     */
    private Uuid $resetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTime $resetTokenCreatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Programme", mappedBy="customers")
     */
    private Collection $programmes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function setProgrammes(Collection $programmes): self
    {
        $this->programmes = $programmes;
        return $this;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function setApiToken(Uuid $apiToken): self
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    public function getResetToken(): Uuid
    {
        return $this->resetToken;
    }

    public function setResetToken(Uuid $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenCreatedAt(): \DateTime
    {
        return $this->resetTokenCreatedAt;
    }

    public function setResetTokenCreatedAt(\DateTime $resetTokenCreatedAt): self
    {
        $this->resetTokenCreatedAt = $resetTokenCreatedAt;
        return $this;
    }

    public static function createUserFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->email = $userDto->email;
        $user->cnp = $userDto->cnp;
        $user->plainPassword = $userDto->password;
        $user->setRoles($user->getRoles());

        return $user;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
