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

    // 👇 La colonne pour "Top", "Base", "Protéines"...
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subCategory = null;

    // 👇 LA NOUVELLE COLONNE POUR "Eco-Green", "Eco-Mix"... 👇
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $diet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

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

    public function getSubCategory(): ?string
    {
        return $this->subCategory;
    }

    public function setSubCategory(?string $subCategory): static
    {
        $this->subCategory = $subCategory;
        return $this;
    }

    // 👇 NOUVEAUX GETTER / SETTER POUR LE RÉGIME 👇
    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): static
    {
        $this->diet = $diet;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
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

    public function __toString(): string
    {
        return $this->name ?? 'Activity type';
    }
}