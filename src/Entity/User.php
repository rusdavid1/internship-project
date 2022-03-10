<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity()
 */
class User //implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column
     */
    public string $password = '';

    /**
     * @ORM\Column
     */
    public string $cnp = '';

    /**
     * @ORM\Column
     */
    public string $email = '';

    /**
     * @ORM\Column
     */
    public string $firstName = '';

    /**
     * @ORM\Column
     */
    public string $lastName = '';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\ManyToMany(targetEntity="Programme", mappedBy="customers")
     */
    private Collection $programmes;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
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

    public function addProgramme(Programme $programme): self
    {
        if($this->programmes->contains($programme)) {
            return $this;
        }

        $this->programmes->add($programme);
        $programme->addCustomer($this);

        return $this;
    }

    public function removeProgramme(Programme $programme): self
    {
        if($this->customers->contains($programme)) {
            return $this;
        }

        $this->customers->remove($programme);
        $programme->removeCustomer($this);

        return $this;
    }

//    public function jsonSerialize(): array
//    {
//        return [
//            "id" => $this->id,
//            "firstName" => $this->firstName,
//            "lastName" => $this->lastName,
//            "email" => $this->email,
//            "cnp" => $this->cnp,
//            "roles" => $this->roles,
//        ];
//    }

    public static function createUserFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->firstName = $userDto->firstName;
        $user->lastName = $userDto->lastName;
        $user->email = $userDto->email;
        $user->cnp = $userDto->cnp;
        $user->password = $userDto->password;
        $user->roles = $userDto->roles;

        return $user;
    }
}
