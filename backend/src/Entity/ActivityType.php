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

    // Sous-catégorie permettant de regrouper les types d'activités au sein d'une même catégorie.
    // Exemples pour la nourriture : 'Base', 'Protéines', 'Vitamines'.
    // Exemples pour les vêtements : 'Top', 'Bas', 'Chaussures'.
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subCategory = null;

    // Champ utilisé uniquement pour la catégorie Nourriture.
    // Il indique à quel "régime" appartient ce type d'aliment : 'Eco-Green', 'Eco-Mix' ou 'Eco-Boost'.
    // Cela permet au Wizard front-end d'afficher les bons aliments selon le régime choisi par l'utilisateur.
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

    // Getters et setters pour le régime alimentaire
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