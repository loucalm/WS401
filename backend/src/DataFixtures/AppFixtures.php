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
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. CRÉATION DES UTILISATEURS DE TEST
        $user1 = new User();
        $user1->setEmail('admin@test.com');
        $user1->setUsername('EcoWarrior');
        $user1->setTargetCo2(2000);
        $user1->setUnitPreference('metric');
        $user1->setRoles([]);
        // Le mot de passe pour tester sera : "test"
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'test'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('lou@test.com');
        $user2->setUsername('loucalm');
        $user2->setTargetCo2(2000);
        $user2->setUnitPreference('metric');
        $user2->setRoles([]);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'test'));
        $manager->persist($user2);


        // 2. CRÉATION DES CATÉGORIES PRINCIPALES
        $catData = [
            'Travel' => ['car', '#3B82F6'],
            'Food' => ['utensils', '#10B981'],
            'Consumption' => ['home', '#4B6481'],
            'Clothing' => ['tshirt', '#831297'],
        ];

        $categories = [];
        foreach ($catData as $name => $info) {
            $cat = new Category();
            $cat->setName($name);
            $cat->setIcon($info[0]);
            $cat->setColor($info[1]);
            $manager->persist($cat);
            $categories[$name] = $cat; // On les stocke pour les lier facilement
        }


        // 3. LE SUPER-CATALOGUE (Activity Types)
        $activities = [
            // Travel
            ['Travel', 'Transport', null, 'Car (Diesel)', 0.19, 'km'],
            ['Travel', 'Transport', null, 'Car (Petrol)', 0.22, 'km'],
            ['Travel', 'Transport', null, 'Car (Electric)', 0.05, 'km'],
            ['Travel', 'Transport', null, 'Train', 0.02, 'km'],
            ['Travel', 'Transport', null, 'Plane', 0.25, 'km'],
            ['Travel', 'Transport', null, 'Walk', 0.0, 'km'],
            ['Travel', 'Transport', null, 'Motorcycle', 0.11, 'km'],
            ['Travel', 'Transport', null, 'Boat', 0.25, 'km'],
            ['Travel', 'Transport', null, 'Tram', 0.02, 'km'],
            ['Travel', 'Transport', null, 'Bus', 0.10, 'km'],
            ['Travel', 'Transport', null, 'Carpooling', 0.10, 'km'],
            ['Travel', 'Transport', null, 'Bicycle', 0.0, 'km'],

            // Food : Eco-Green
            ['Food', 'Base', 'Eco-Green', 'Starchy foods (Potatoes)', 0.2, 'portion'],
            ['Food', 'Base', 'Eco-Green', 'Cereals (Rice, Pasta, Quinoa)', 0.3, 'portion'],
            ['Food', 'Protéines', 'Eco-Green', 'Tofu', 0.2, 'portion'],
            ['Food', 'Protéines', 'Eco-Green', 'Seitan', 0.2, 'portion'],
            ['Food', 'Protéines', 'Eco-Green', 'Legumin', 0.1, 'portion'],
            ['Food', 'Vitamines', 'Eco-Green', 'Seasonal vegetables fresh', 0.1, 'portion'],
            ['Food', 'Vitamines', 'Eco-Green', 'Seasonal vegetables frozen', 0.3, 'portion'],
            ['Food', 'Bonus', 'Eco-Green', 'Seeds', 0.05, 'portion'],
            ['Food', 'Bonus', 'Eco-Green', 'Oleaginous', 0.05, 'portion'],

            // Food : Eco-Mix
            ['Food', 'Base', 'Eco-Mix', 'Rice', 0.8, 'portion'],
            ['Food', 'Base', 'Eco-Mix', 'Pasta', 0.5, 'portion'],
            ['Food', 'Base', 'Eco-Mix', 'Vegetables', 0.2, 'portion'],
            ['Food', 'Protéines', 'Eco-Mix', 'White meat', 1.5, 'portion'],
            ['Food', 'Protéines', 'Eco-Mix', 'Fish', 1.2, 'portion'],
            ['Food', 'Protéines', 'Eco-Mix', 'Eggs', 0.5, 'portion'],
            ['Food', 'Vitamines', 'Eco-Mix', 'Hard cheese', 1.0, 'portion'],
            ['Food', 'Vitamines', 'Eco-Mix', 'Yaourt', 0.4, 'portion'],
            ['Food', 'Bonus', 'Eco-Mix', 'Salad', 0.1, 'portion'],
            ['Food', 'Bonus', 'Eco-Mix', 'Cooked vegetables', 0.2, 'portion'],

            // Food : Eco-Boost
            ['Food', 'Base', 'Eco-Boost', 'Fries', 0.5, 'portion'],
            ['Food', 'Base', 'Eco-Boost', 'Puree', 0.4, 'portion'],
            ['Food', 'Base', 'Eco-Boost', 'Pasta', 0.5, 'portion'],
            ['Food', 'Protéines', 'Eco-Boost', 'Red meat (Beef, Lamb)', 7.0, 'portion'],
            ['Food', 'Protéines', 'Eco-Boost', 'Charcuterie', 4.0, 'portion'],
            ['Food', 'Vitamines', 'Eco-Boost', 'Hard cheese', 1.0, 'portion'],
            ['Food', 'Vitamines', 'Eco-Boost', 'Yaourt', 0.4, 'portion'],
            ['Food', 'Bonus', 'Eco-Boost', 'Fastfood', 3.5, 'portion'],
            ['Food', 'Bonus', 'Eco-Boost', 'Processed dish', 2.5, 'portion'],

            // Consumption
            ['Consumption', 'Energy', null, 'Electricity', 0.1, 'kWh'],
            ['Consumption', 'Energy', null, 'Fuel', 0.25, 'kWh'],
            ['Consumption', 'Energy', null, 'Wood', 0.04, 'kWh'],
            ['Consumption', 'Energy', null, 'Gas', 0.2, 'kWh'],
            ['Consumption', 'Energy', null, 'Solar', 0.0, 'kWh'],
            ['Consumption', 'Energy', null, 'Wind', 0.0, 'kWh'],

            // Clothing
            ['Clothing', 'Top', null, 'T-shirt', 5.0, 'item'],
            ['Clothing', 'Top', null, 'Hoodie', 15.0, 'item'],
            ['Clothing', 'Top', null, 'Coat', 25.0, 'item'],
            ['Clothing', 'Stocking', null, 'Pants', 10.0, 'item'],
            ['Clothing', 'Stocking', null, 'Shorts', 6.0, 'item'],
            ['Clothing', 'Stocking', null, 'Sweatpant', 8.0, 'item'],
            ['Clothing', 'Stocking', null, 'Socks', 1.0, 'pair'],
            ['Clothing', 'Shoes', null, 'Shoes', 12.0, 'pair'],
            ['Clothing', 'Underwear', null, 'Underwear', 2.0, 'item'],
            ['Clothing', 'Underwear', null, 'Bra', 4.0, 'item'],
            ['Clothing', 'Accessory', null, 'Cap / Hat', 3.0, 'item'],
            ['Clothing', 'Accessory', null, 'Glasses', 2.0, 'item'],
            ['Clothing', 'Accessory', null, 'Scarf', 3.0, 'item'],
        ];

        foreach ($activities as $act) {
            $activityType = new ActivityType();
            $activityType->setCategory($categories[$act[0]]);
            $activityType->setSubCategory($act[1]);
            $activityType->setDiet($act[2]);
            $activityType->setName($act[3]);
            $activityType->setCo2Factor($act[4]);
            $activityType->setUnitLabel($act[5]);
            $manager->persist($activityType);
        }

        // On exécute le tout en base de données
        $manager->flush();
    }
}