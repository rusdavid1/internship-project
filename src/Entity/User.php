<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use App\Controller\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
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
     * @Groups("api:programme:all")
     */
    private int $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Regex("/^\p{Lu}\p{L}+$/")
     * @Groups("api:programme:all")
     */
    public string $firstName = '';

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Regex("/^\p{Lu}\p{L}+$/")
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
     * @ORM\Column(type="string")
     * @MyAssert\Password()
     */
    private string $password;

    /**
    * @ORM\Column(type="uuid", unique=true, nullable=true)
    */
    private Uuid $apiToken;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private ?\DateTime $deletedAt;

    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     */
    private Uuid $resetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $resetTokenCreatedAt;

    /**
     * @ORM\Column (type="string")
     * @Assert\Length(min=4, max=20)
     * @Assert\Regex("/^[0-9+ ()-]*$/")
     */
    public string $phoneNumber = '';

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

    public function getApiToken(): Uuid
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

    public function getResetTokenCreatedAt(): ?\DateTime
    {
        return $this->resetTokenCreatedAt;
    }

    public function setResetTokenCreatedAt(?\DateTime $resetTokenCreatedAt): self
    {
        $this->resetTokenCreatedAt = $resetTokenCreatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): User
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function addProgramme(Programme $programme): self
    {
        if ($this->programmes->contains($programme)) {
            return $this;
        }

        $this->programmes->add($programme);
        $programme->addCustomer($this);

        return $this;
    }

    public function removeProgramme(Programme $programme): self
    {
        if (!$this->programmes->contains($programme)) {
            return $this;
        }

        $this->programmes->remove($programme);
        $programme->removeCustomer($this);

        return $this;
    }

    public static function createUserFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->email = $userDto->email;
        $user->cnp = $userDto->cnp;
        $user->phoneNumber = $userDto->phoneNumber;
        $user->password = $userDto->password;
        $user->setRoles($user->getRoles());

        return $user;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
    }
}
