<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Entry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column]
    private ?float $totalCo2 = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Champ JSON pour stocker des informations complémentaires libres sur l'entrée.
    // Par exemple pour un trajet : { "distance": 15, "transportMode": "voiture" }.
    // Ce champ est optionnel et son contenu dépend du type d'activité.
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $details = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    // Relation vers les lignes de détail de l'entrée (comme les lignes d'une commande).
    // Chaque EntryItem représente un type d'activité avec sa quantité.
    // La cascade 'persist' et 'remove' signifie que si on sauvegarde ou supprime une Entry,
    // ses EntryItems sont automatiquement sauvegardés ou supprimés avec elle.
    #[ORM\OneToMany(mappedBy: 'entry', targetEntity: EntryItem::class, cascade: ['persist', 'remove'])]
    private Collection $entryItems;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        // On initialise la collection d'items pour éviter les erreurs PHP quand on y accède
        // avant que l'entrée ne soit enregistrée en base de données.
        $this->entryItems = new ArrayCollection();
    }

    /**
     * Calcule automatiquement le total CO2 de l'entrée avant chaque sauvegarde en base.
     * Symfony (Doctrine) appelle cette méthode automatiquement grâce aux attributs
     * PrePersist (avant l'insertion) et PreUpdate (avant la mise à jour).
     * On parcourt tous les items liés et on fait la somme : quantité * facteur CO2 de chaque activité.
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotalCo2(): void
    {
        $total = 0;
        foreach ($this->entryItems as $item) {
            if ($item->getQuantity() !== null && $item->getActivityType() !== null) {
                $total += $item->getQuantity() * $item->getActivityType()->getCo2Factor();
            }
        }
        $this->totalCo2 = $total;
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getTotalCo2(): ?float
    {
        return $this->totalCo2;
    }

    public function setTotalCo2(?float $totalCo2): static
    {
        $this->totalCo2 = $totalCo2;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): static
    {
        $this->details = $details;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, EntryItem>
     */
    public function getEntryItems(): Collection
    {
        return $this->entryItems;
    }

    public function addEntryItem(EntryItem $entryItem): static
    {
        if (!$this->entryItems->contains($entryItem)) {
            $this->entryItems->add($entryItem);
            $entryItem->setEntry($this);
        }
        return $this;
    }

    public function removeEntryItem(EntryItem $entryItem): static
    {
        if ($this->entryItems->removeElement($entryItem)) {
            // On met le côté propriétaire de la relation à null pour couper le lien
            // en base de données et éviter des liens orphelins.
            if ($entryItem->getEntry() === $this) {
                $entryItem->setEntry(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        if ($this->id === null) {
            return 'Entry';
        }

        $owner = $this->owner?->__toString() ?? 'Unknown user';

        return sprintf('#%d - %s', $this->id, $owner);
    }
}