<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity()
 */
class Building
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime", nullable="false")
     */
    private \DateTime $startDate;

    /**
     * @ORM\Column(type="datetime", nullable="false")
     */
    private \DateTime $endDate;

    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="building")
     * @ORM\JoinTable(name="buildings")
     */
    private ArrayCollection $rooms;

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

    public function getRooms(): ArrayCollection
    {
        return $this->rooms;
    }

    public function setRooms(ArrayCollection $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }
}
