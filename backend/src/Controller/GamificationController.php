<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\GamificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GamificationController extends AbstractController
{
    public function __construct(
        private readonly GamificationService $gamificationService,
    ) {
    }

    #[Route('/api/me/gamification-summary', name: 'api_me_gamification_summary', methods: ['GET'])]
    public function summary(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($this->gamificationService->buildSummary($user));
    }

    #[Route('/api/leaderboard', name: 'api_leaderboard', methods: ['GET'])]
    public function leaderboard(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $scope = (string) $request->query->get('scope', 'friends');
        if (!in_array($scope, ['friends', 'all'], true)) {
            $scope = 'friends';
        }

        $period = (string) $request->query->get('period', 'daily');
        if ($period !== 'daily') {
            $period = 'daily';
        }

        return $this->json($this->gamificationService->buildLeaderboard($user, $scope, $period));
    }
}
