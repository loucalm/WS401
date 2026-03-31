<?php

namespace App\DataFixtures;

use App\Entity\ActivityType;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. CRÉATION DES UTILISATEURS (Avec position GPS pour la carte)
        $usersData = [
            ['email' => 'admin@test.com', 'username' => 'EcoWarrior', 'lat' => 48.8566, 'lng' => 2.3522], // Paris
            ['email' => 'radouan@test.com', 'username' => 'Radouan', 'lat' => 45.7640, 'lng' => 4.8357], // Lyon
            ['email' => 'marwan@test.com', 'username' => 'Marwan', 'lat' => 43.2965, 'lng' => 5.3698], // Marseille
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setLatitude($data['lat']);
            $user->setLongitude($data['lng']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $manager->persist($user);
            $users[] = $user;
        }

        // 2. CRÉATION DES CATÉGORIES
        $catTransport = new Category();
        $catTransport->setName('Transport')->setIcon('car')->setColor('#3B82F6'); // Bleu
        $manager->persist($catTransport);

        $catRepas = new Category();
        $catRepas->setName('Alimentation')->setIcon('utensils')->setColor('#10B981'); // Vert
        $manager->persist($catRepas);

        // 3. CRÉATION DES TYPES D'ACTIVITÉS (Avec vrais facteurs CO2 de l'ADEME)
        $activities = [
            // Transports (facteur en kg CO2 par km)
            ['name' => 'Voiture (Thermique)', 'factor' => 0.22, 'unit' => 'km', 'cat' => $catTransport],
            ['name' => 'Voiture (Électrique)', 'factor' => 0.05, 'unit' => 'km', 'cat' => $catTransport],
            ['name' => 'TGV', 'factor' => 0.002, 'unit' => 'km', 'cat' => $catTransport],
            ['name' => 'Avion (Court courrier)', 'factor' => 0.25, 'unit' => 'km', 'cat' => $catTransport],
            ['name' => 'Vélo / Marche', 'factor' => 0.0, 'unit' => 'km', 'cat' => $catTransport],
            
            // Alimentation (facteur en kg CO2 par repas)
            ['name' => 'Repas avec Bœuf', 'factor' => 7.0, 'unit' => 'repas', 'cat' => $catRepas],
            ['name' => 'Repas Végétarien', 'factor' => 0.5, 'unit' => 'repas', 'cat' => $catRepas],
            ['name' => 'Repas Poulet/Porc', 'factor' => 1.5, 'unit' => 'repas', 'cat' => $catRepas],
        ];

        foreach ($activities as $actData) {
            $activity = new ActivityType();
            $activity->setName($actData['name']);
            $activity->setCo2Factor($actData['factor']);
            $activity->setUnitLabel($actData['unit']);
            $activity->setCategory($actData['cat']);
            $manager->persist($activity);
        }

        // On envoie tout dans la base de données !
        $manager->flush();
    }
}