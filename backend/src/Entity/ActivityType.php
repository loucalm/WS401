<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class ActivityType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $co2Factor = null;

    #[ORM\Column(length: 50)]
    private ?string $unitLabel = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCo2Factor(): ?float
    {
        return $this->co2Factor;
    }

    public function setCo2Factor(float $co2Factor): static
    {
        $this->co2Factor = $co2Factor;
        return $this;
    }

    public function getUnitLabel(): ?string
    {
        return $this->unitLabel;
    }

    public function setUnitLabel(string $unitLabel): static
    {
        $this->unitLabel = $unitLabel;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }
}