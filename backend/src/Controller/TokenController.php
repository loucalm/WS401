<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TokenController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtTokenManager,
    ) {
    }

    #[Route('/api/token/refresh-authenticated', name: 'api_token_refresh_authenticated', methods: ['POST'])]
    public function refresh(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'token' => $this->jwtTokenManager->create($user),
        ]);
    }
}