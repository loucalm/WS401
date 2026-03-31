<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class Friendship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null; // Celui qui invite

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null; // Celui qui reçoit l'invitation

    #[ORM\Column(length: 20)]
    private ?string $status = 'pending'; // 'pending' ou 'accepted'

    // Getters et Setters...
}