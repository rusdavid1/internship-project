<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 *@ORM\Entity()
 */
class Programme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column()
     */
    public string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $description = '';

    /**
     * @ORM\Column(type="datetime")
     * @MyAssert\Date()
     */
    private \DateTime $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @MyAssert\Date()
     */
    private \DateTime $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private ?User $trainer;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn (name="room_id", referencedColumnName="id")
     */
    private Room $room;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="programmes")
     * @ORM\JoinTable(name="programmes_customers")
     */
    private Collection $customers;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isOnline = false;

    /**
     * @ORM\Column (type="integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    public int $maxParticipants = 0;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getTrainer(): ?User
    {
        return $this->trainer;
    }

    public function setTrainer(?User $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }

    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function setCustomers(ArrayCollection $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    public function addCustomer(User $customer): self
    {
        if ($this->customers->contains($customer)) {
            return $this;
        }

        $this->customers->add($customer);
        $customer->addProgramme($this);

        return $this;
    }

    public function removeCustomer(User $customer): self
    {
        if ($this->customers->contains($customer)) {
            return $this;
        }

        $this->customers->remove($customer);
        $customer->removeProgramme($this);

        return $this;
    }
}
