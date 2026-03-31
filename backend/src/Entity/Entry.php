<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
#[ORM\HasLifecycleCallbacks] // Permet d'exécuter du code automatiquement
class Entry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $value = null; // La distance ou la quantité saisie

    #[ORM\Column]
    private ?float $totalCo2 = null; // Le résultat final

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null; // A qui appartient cette saisie ?

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityType $activityType = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ASTUCE MAGIQUE : Avant de sauvegarder en base, on calcule le CO2 automatiquement !
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotalCo2(): void
    {
        if ($this->value !== null && $this->activityType !== null) {
            $this->totalCo2 = $this->value * $this->activityType->getCo2Factor();
        }
    }

    // Getters et Setters...
}