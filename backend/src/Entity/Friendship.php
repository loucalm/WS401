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
    private ?User $sender = null; // L'utilisateur qui a envoyé la demande d'amitié

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null; // L'utilisateur qui a reçu la demande d'amitié

    #[ORM\Column(length: 20)]
    private ?string $status = 'pending'; // Statut de la demande : 'pending' (en attente) ou 'accepted' (acceptée)

    // Getters et setters à ajouter si nécessaire
}