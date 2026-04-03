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

    // NOUVEAU : Le champ JSON pour stocker { "distance": 15, "origin": "thrift" }
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $details = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    // NOUVEAU : La relation vers le contenu du panier (EntryItem)
    #[ORM\OneToMany(mappedBy: 'entry', targetEntity: EntryItem::class, cascade: ['persist', 'remove'])]
    private Collection $entryItems;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->entryItems = new ArrayCollection(); // On initialise la collection
    }

    // NOUVEAU CALCUL MAGIQUE : On boucle sur tous les items du panier !
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
        $this->value = $total;
        $this->totalCo2 = $total;
    }

    // --- GETTERS & SETTERS ---

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
            // set the owning side to null (unless already changed)
            if ($entryItem->getEntry() === $this) {
                $entryItem->setEntry(null);
            }
        }
        return $this;
    }
}