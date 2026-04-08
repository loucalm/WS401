<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    private ?float $targetCo2 = 2000.0; // Objectif CO2 journalier de l'utilisateur, en grammes. Par défaut 2000g soit 2kg.

    #[ORM\Column(length: 20)]
    private ?string $unitPreference = 'metric'; // Préférence d'unité de mesure : 'metric' (km, kg) ou 'imperial' (miles, lbs)

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        // Symfony utilise cette méthode pour identifier l'utilisateur dans la session et les tokens JWT.
        // On retourne l'email car c'est le champ unique qui sert d'identifiant dans notre app.
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // On s'assure que chaque utilisateur possède au minimum ROLE_USER,
        // même si la colonne roles est vide en base de données.
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    // Getters et setters pour les champs spécifiques à notre application

   public function getUsername(): string
    {
        return $this->username ?: (string) $this->email;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getTargetCo2(): ?float
    {
        return $this->targetCo2;
    }

    public function setTargetCo2(?float $targetCo2): static
    {
        $this->targetCo2 = $targetCo2;

        return $this;
    }

    public function getUnitPreference(): ?string
    {
        return $this->unitPreference;
    }

    public function setUnitPreference(string $unitPreference): static
    {
        $this->unitPreference = $unitPreference;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Méthode de sérialisation de l'utilisateur pour la session Symfony.
     * On ne stocke pas le hash complet du mot de passe dans la session pour des raisons de sécurité.
     * À la place, on stocke un hash CRC32C du mot de passe, ce qui permet à Symfony
     * de détecter un changement de mot de passe et d'invalider la session automatiquement.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }

    // Champ virtuel utilisé uniquement dans les formulaires (EasyAdmin, inscription).
    // Il n'est jamais persisté en base de données : on l'utilise pour récupérer
    // le mot de passe en clair, le hasher, puis le stocker dans le champ $password.
    private ?string $plainPassword = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
}