<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(
        "24 hours",
        message: "Les réservations doivent se faire au moins 24 heures à l'avance."
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?\DateInterval $timeSlot = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    #[ORM\ManyToOne(inversedBy: 'relations')]
    private ?User $relations = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTimeSlot(): ?\DateInterval
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(\DateInterval $timeSlot): static
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function getRelations(): ?User
    {
        return $this->relations;
    }

    public function setRelations(?User $relations): static
    {
        $this->relations = $relations;

        return $this;
    }

    public function validateUniqueTimeSlot(array $existingReservations): void
    {
        foreach ($existingReservations as $reservation) {
            if ($reservation->getDate() == $this->getDate() && $reservation->getTimeSlot() == $this->getTimeSlot()) {
                throw new \InvalidArgumentException("Already booked for this time slot.");
            }
        }
    }
}