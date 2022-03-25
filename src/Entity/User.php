<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column
     * @MyAssert\Password()
     */
    public string $password = '';

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
     * @ORM\ManyToMany(targetEntity="Programme", mappedBy="customers")
     */
    private Collection $programmes;

    public function getId(): ?int
    {
        return $this->id;
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
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public static function createUserFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->email = $userDto->email;
        $user->cnp = $userDto->cnp;
        $user->password = $userDto->password;
        $user->setRoles(['customer']);

        return $user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
