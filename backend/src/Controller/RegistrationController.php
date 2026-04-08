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
        // On récupère le corps de la requête HTTP et on le décode depuis le format JSON.
        $data = json_decode($request->getContent(), true);

        // Vérification minimale : l'email et le mot de passe sont obligatoires.
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email et mot de passe requis.'], 400);
        }

        // On vérifie qu'aucun compte n'existe déjà avec cet email.
        // Si c'est le cas, on renvoie une erreur 409 (conflit) pour l'indiquer au front-end.
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], 409);
        }

        // Création d'un nouvel objet User et remplissage de ses champs.
        $user = new User();
        $user->setEmail($data['email']);
        $user->setProfilePicture('default.jpg');
        $user->setLatitude(48.24);
        $user->setLongitude(4.75);
        
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        // On ne stocke jamais le mot de passe en clair en base de données.
        // Le PasswordHasher de Symfony applique un algorithme de hachage sécurisé (bcrypt/argon2).
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // persist() indique à Doctrine de suivre cet objet.
        // flush() exécute la requête SQL INSERT en base de données.
        $entityManager->persist($user);
        $entityManager->flush();

        // On retourne un code 201 (Created) pour confirmer la création au client.
        return new JsonResponse(['message' => 'Utilisateur créé avec succès !'], 201);
    }
}