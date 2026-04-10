<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/api/me/profile', name: 'api_me_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $username = trim((string) $request->request->get('username', ''));
        if ($username === '') {
            return $this->json(['message' => 'Username is required.'], Response::HTTP_BAD_REQUEST);
        }

        $targetCo2Raw = $request->request->get('targetCo2');
        if ($targetCo2Raw === null || $targetCo2Raw === '' || !is_numeric($targetCo2Raw)) {
            return $this->json(['message' => 'Target CO2 is required.'], Response::HTTP_BAD_REQUEST);
        }

        $targetCo2Kg = (float) $targetCo2Raw;
        if ($targetCo2Kg <= 0) {
            return $this->json(['message' => 'Target CO2 must be greater than zero.'], Response::HTTP_BAD_REQUEST);
        }

        $clearPicture = filter_var((string) $request->request->get('clearPicture', '0'), FILTER_VALIDATE_BOOL);

        $currentPassword = (string) $request->request->get('currentPassword', '');
        $newPassword = trim((string) $request->request->get('newPassword', ''));
        $confirmPassword = trim((string) $request->request->get('confirmPassword', ''));


        $profilePicture = $request->request->get('profilePicture');
        if (is_string($profilePicture) && $profilePicture !== '') {
            $user->setProfilePicture($profilePicture);
        } elseif (filter_var((string) $request->request->get('clearPicture', '0'), FILTER_VALIDATE_BOOL)) {
            $user->setProfilePicture(null);
        }

        if ($newPassword !== '' || $confirmPassword !== '' || $currentPassword !== '') {
            if ($currentPassword === '') {
                return $this->json(['message' => 'Current password is required.'], Response::HTTP_BAD_REQUEST);
            }

            if ($newPassword === '') {
                return $this->json(['message' => 'New password is required.'], Response::HTTP_BAD_REQUEST);
            }

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                return $this->json(['message' => 'Current password is incorrect.'], Response::HTTP_BAD_REQUEST);
            }

            if ($newPassword !== $confirmPassword) {
                return $this->json(['message' => 'Password confirmation does not match.'], Response::HTTP_BAD_REQUEST);
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        }

        $user->setUsername($username);
        $user->setTargetCo2($targetCo2Kg);

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Profile updated successfully.',
            'user' => [
                '@id' => '/api/users/' . $user->getId(),
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'targetCo2' => $user->getTargetCo2(),
                'profilePicture' => $user->getProfilePicture(),
            ],
        ]);
    }



}