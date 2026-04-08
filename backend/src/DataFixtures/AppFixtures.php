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
        // Création de deux utilisateurs de test avec des mots de passe hachés.
        // Ces utilisateurs servent à tester l'authentification sans créer de compte manuellement.
        $user1 = new User();
        $user1->setEmail('admin@test.com');
        $user1->setUsername('EcoWarrior');
        $user1->setTargetCo2(2000);
        $user1->setUnitPreference('metric');
        $user1->setRoles(['ROLE_ADMIN']);
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


        // Création des catégories principales qui regroupent les types d'activités.
        // Chaque catégorie a un nom, une icône (format Iconify) et une couleur CSS.
        $catData = [
            'Travel' => ['mdi:travel', '--color-main'],
            'Food' => ['mdi:food', '--color-tertiary'],
            'Consumption' => ['material-symbols:home-rounded', '--color-secondary'],
            'Clothing' => ['tabler:shirt-filled', '--color-cloth'],
        ];

        $categories = [];
        foreach ($catData as $name => $info) {
            $cat = new Category();
            $cat->setName($name);
            $cat->setIcon($info[0]);
            $cat->setColor($info[1]);
            $manager->persist($cat);
            $categories[$name] = $cat; // On les indexe par nom pour les réutiliser lors de la création des ActivityTypes
        }


        // Catalogue complet des types d'activités avec leurs facteurs d'émission CO2.
        // Format de chaque ligne : [Catégorie, Sous-catégorie, Régime, Nom, FacteurCO2, Unité, Icône (optionnel)]
        // Le facteur CO2 est exprimé en kg de CO2 émis par unité (km, portion, kWh, article...).
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
            ['Food', 'Base', 'Eco-Green', 'Starchy foods (Potatoes)', 0.2, 'portion', 'fluent-emoji-high-contrast:potato'],
            ['Food', 'Base', 'Eco-Green', 'Cereals (Rice, Pasta, Quinoa)', 0.3, 'portion', 'fa6-solid:bowl-rice'],
            ['Food', 'Proteins', 'Eco-Green', 'Tofu', 0.2, 'portion', 'simple-icons:opentofu'],
            ['Food', 'Proteins', 'Eco-Green', 'Seitan', 0.2, 'portion', 'hugeicons:bread-03'],
            ['Food', 'Proteins', 'Eco-Green', 'Legumes', 0.1, 'portion', 'mynaui:sprout-solid'],
            ['Food', 'Vitamins', 'Eco-Green', 'Seasonal vegetables fresh', 0.1, 'portion', 'flowbite:carrot-solid'],
            ['Food', 'Vitamins', 'Eco-Green', 'Seasonal vegetables frozen', 0.3, 'portion', 'flowbite:carrot-solid'],
            ['Food', 'Bonus', 'Eco-Green', 'Seeds', 0.05, 'portion', 'mdi:seed'],
            ['Food', 'Bonus', 'Eco-Green', 'Oily Seeds / Nuts', 0.05, 'portion', 'game-icons:sunflower'],

            // Food : Eco-Mix
            ['Food', 'Base', 'Eco-Mix', 'Rice', 0.8, 'portion', 'mdi:rice'],
            ['Food', 'Base', 'Eco-Mix', 'Pasta', 0.5, 'portion', 'mdi:spaghetti'],
            ['Food', 'Base', 'Eco-Mix', 'Vegetables', 0.2, 'portion', 'flowbite:carrot-solid'],
            ['Food', 'Proteins', 'Eco-Mix', 'White meat', 1.5, 'portion', 'mdi:food-drumstick'],
            ['Food', 'Proteins', 'Eco-Mix', 'Fish', 1.2, 'portion', 'mdi:fish'],
            ['Food', 'Proteins', 'Eco-Mix', 'Eggs', 0.5, 'portion', 'boxicons:egg-filled'],
            ['Food', 'Vitamins', 'Eco-Mix', 'Hard cheese', 1.0, 'portion', 'boxicons:cheese-filled'],
            ['Food', 'Vitamins', 'Eco-Mix', 'Yogurt', 0.4, 'portion', 'mdi:cup'],
            ['Food', 'Bonus', 'Eco-Mix', 'Salad', 0.1, 'portion', 'tabler:salad-filled'],
            ['Food', 'Bonus', 'Eco-Mix', 'Cooked vegetables', 0.2, 'portion', 'flowbite:carrot-solid'],

            // Food : Eco-Boost
            ['Food', 'Base', 'Eco-Boost', 'Fries', 0.5, 'portion', 'mingcute:fries-fill'],
            ['Food', 'Base', 'Eco-Boost', 'Mashed Potatoes', 0.4, 'portion', 'mdi:bowl-mix'],
            ['Food', 'Base', 'Eco-Boost', 'Pasta', 0.5, 'portion', 'mdi:spaghetti'],
            ['Food', 'Proteins', 'Eco-Boost', 'Red meat (Beef, Lamb)', 7.0, 'portion', 'mdi:cow'],
            ['Food', 'Proteins', 'Eco-Boost', 'Cured meats', 4.0, 'portion', 'mdi:sausage'],
            ['Food', 'Vitamins', 'Eco-Boost', 'Hard cheese', 1.0, 'portion', 'boxicons:cheese-filled'],
            ['Food', 'Vitamins', 'Eco-Boost', 'Yogurt', 0.4, 'portion', 'mdi:cup'],
            ['Food', 'Bonus', 'Eco-Boost', 'Fast food', 3.5, 'portion', 'mdi:hamburger'],
            ['Food', 'Bonus', 'Eco-Boost', 'Processed dish', 2.5, 'portion', 'mdi:food'],

            // Consumption
            ['Consumption', 'Energy', null, 'Electricity', 0.1, 'kWh'],
            ['Consumption', 'Energy', null, 'Fuel', 0.25, 'kWh'],
            ['Consumption', 'Energy', null, 'Wood', 0.04, 'kWh'],
            ['Consumption', 'Energy', null, 'Gas', 0.2, 'kWh'],
            ['Consumption', 'Energy', null, 'Solar', 0.0, 'kWh'],
            ['Consumption', 'Energy', null, 'Wind', 0.0, 'kWh'],

            // Clothing
            ['Clothing', 'Top', null, 'T-shirt', 5.0, 'item', 'tabler:shirt-filled'],
            ['Clothing', 'Top', null, 'Hoodie', 15.0, 'item', 'hugeicons:hoodie'],
            ['Clothing', 'Top', null, 'Coat', 25.0, 'item', 'icon-park-outline:women-coat'],
            ['Clothing', 'Bottoms', null, 'Pants', 10.0, 'item', 'boxicons:pant-filled'],
            ['Clothing', 'Bottoms', null, 'Shorts', 6.0, 'item', 'icon-park-solid:clothes-pants-short'],
            ['Clothing', 'Bottoms', null, 'Sweatpants', 8.0, 'item', 'ph:pants-fill'],
            ['Clothing', 'Bottoms', null, 'Socks', 1.0, 'pair', 'fa7-solid:socks'],
            ['Clothing', 'Shoes', null, 'Shoes', 12.0, 'pair', 'ph:sneaker-fill'],
            ['Clothing', 'Underwear', null, 'Underwear', 2.0, 'item', 'mdi:briefs-outline'],
            ['Clothing', 'Underwear', null, 'Bra', 4.0, 'item', 'lucide-lab:bra-sports'],
            ['Clothing', 'Accessory', null, 'Cap / Hat', 3.0, 'item', 'fluent-emoji-high-contrast:billed-cap'],
            ['Clothing', 'Accessory', null, 'Glasses', 2.0, 'item', 'mdi:glasses'],
            ['Clothing', 'Accessory', null, 'Scarf', 3.0, 'item', 'mingcute:scarf-fill'],
        ];

        foreach ($activities as $act) {
            $activityType = new ActivityType();
            $activityType->setCategory($categories[$act[0]]);
            $activityType->setSubCategory($act[1]);
            $activityType->setDiet($act[2]);
            $activityType->setName($act[3]);
            $activityType->setCo2Factor($act[4]);
            $activityType->setUnitLabel($act[5]);
            $activityType->setIcon($act[6] ?? null);
            $manager->persist($activityType);
        }

        // On sauvegarde toutes les entités créées en base de données en une seule opération.
        $manager->flush();
    }
}