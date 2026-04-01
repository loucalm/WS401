<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'app_api_register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // 1. On récupère les données envoyées par le Front (en JSON)
        $data = json_decode($request->getContent(), true);

        // 2. Vérification de base
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email et mot de passe requis.'], 400);
        }

        // 3. Vérifier si l'email existe déjà dans la BDD
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], 409);
        }

        // 4. Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        // 5. Hachage du mot de passe (TRÈS IMPORTANT)
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // 6. Sauvegarde dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // 7. On renvoie un message de succès
        return new JsonResponse(['message' => 'Utilisateur créé avec succès !'], 201);
    }
}