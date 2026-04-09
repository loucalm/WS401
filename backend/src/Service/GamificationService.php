<?php

namespace App\Service;

use App\Entity\Entry;
use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GamificationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function buildSummary(User $user): array
    {
        $targetCo2Kg = $this->normalizeTargetCo2Kg($user->getTargetCo2());
        $entries = $this->entityManager->getRepository(Entry::class)->findBy([
            'owner' => $user,
        ]);

        $dayTotals = [];
        $dayPoints = [];
        $todayKey = (new \DateTimeImmutable())->format('Y-m-d');

        foreach ($entries as $entry) {
            $createdAt = $entry->getCreatedAt();
            if (!$createdAt) {
                continue;
            }

            $dayKey = $createdAt->format('Y-m-d');
            $dayTotals[$dayKey] = ($dayTotals[$dayKey] ?? 0.0) + (float) ($entry->getTotalCo2() ?? 0.0);
            $dayPoints[$dayKey] = ($dayPoints[$dayKey] ?? 0) + $this->entryPoints($entry, $targetCo2Kg);
        }

        $dailyCo2Kg = (float) ($dayTotals[$todayKey] ?? 0.0);
        $dailyPoints = (int) ($dayPoints[$todayKey] ?? 0);
        $dailyScore = $dailyPoints;

        $xpTotal = 0;
        foreach ($entries as $entry) {
            $xpTotal += $this->entryPoints($entry, $targetCo2Kg);
        }

        $level = 1 + intdiv($xpTotal, 500);

        return [
            'user' => [
                '@id' => '/api/users/' . $user->getId(),
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ],
            'dailyCo2Kg' => round($dailyCo2Kg, 3),
            'targetCo2Kg' => round($targetCo2Kg, 3),
            'dailyScore' => $dailyScore,
            'dailyPoints' => $dailyPoints,
            'xpTotal' => $xpTotal,
            'level' => $level,
        ];
    }

    public function buildLeaderboard(User $currentUser, string $scope = 'friends', string $period = 'daily'): array
    {
        if ($period !== 'daily') {
            $period = 'daily';
        }

        $users = $scope === 'all'
            ? $this->entityManager->getRepository(User::class)->findAll()
            : $this->buildFriendsScopeUsers($currentUser);

        if (count($users) === 0) {
            return [
                'scope' => $scope,
                'period' => $period,
                'totalUsers' => 0,
                'users' => [],
            ];
        }

        $entries = $this->entityManager->getRepository(Entry::class)->findBy([
            'owner' => $users,
        ]);

        $todayKey = (new \DateTimeImmutable())->format('Y-m-d');
        $todayTotalsByUserId = [];
        $todayCountByUserId = [];
        $todayPointsByUserId = [];

        foreach ($entries as $entry) {
            $createdAt = $entry->getCreatedAt();
            if (!$createdAt || $createdAt->format('Y-m-d') !== $todayKey) {
                continue;
            }

            $owner = $entry->getOwner();
            if (!$owner || $owner->getId() === null) {
                continue;
            }

            $userId = $owner->getId();
            $todayTotalsByUserId[$userId] = ($todayTotalsByUserId[$userId] ?? 0.0) + (float) ($entry->getTotalCo2() ?? 0.0);
            $todayCountByUserId[$userId] = ($todayCountByUserId[$userId] ?? 0) + 1;
            $todayPointsByUserId[$userId] = ($todayPointsByUserId[$userId] ?? 0) + $this->entryPoints($entry, $this->normalizeTargetCo2Kg($owner->getTargetCo2()));
        }

        $ranked = array_map(function (User $user) use ($currentUser, $todayTotalsByUserId, $todayCountByUserId, $todayPointsByUserId): array {
            $userId = (int) $user->getId();
            $dailyCo2Kg = (float) ($todayTotalsByUserId[$userId] ?? 0.0);
            $targetCo2Kg = $this->normalizeTargetCo2Kg($user->getTargetCo2());

            return [
                'id' => $userId,
                'name' => $user->getUsername(),
                'isCurrentUser' => $userId === (int) $currentUser->getId(),
                'dailyCo2Kg' => round($dailyCo2Kg, 3),
                'activityCount' => (int) ($todayCountByUserId[$userId] ?? 0),
                'points' => (int) ($todayPointsByUserId[$userId] ?? 0),
                'targetCo2Kg' => round($targetCo2Kg, 3),
                'profilePicture' => $user->getProfilePicture(),
                'latitude' => $user->getLatitude(),
                'longitude' => $user->getLongitude(),
            ];
        }, $users);

        usort($ranked, function (array $a, array $b): int {
            if ($b['points'] !== $a['points']) {
                return $b['points'] <=> $a['points'];
            }

            if ($a['dailyCo2Kg'] !== $b['dailyCo2Kg']) {
                return $a['dailyCo2Kg'] <=> $b['dailyCo2Kg'];
            }

            return strcmp((string) $a['name'], (string) $b['name']);
        });

        foreach ($ranked as $index => &$entry) {
            $entry['rank'] = $index + 1;
        }
        unset($entry);

        return [
            'scope' => $scope,
            'period' => $period,
            'totalUsers' => count($ranked),
            'users' => $ranked,
        ];
    }

    private function buildFriendsScopeUsers(User $currentUser): array
    {
        $friendships = $this->entityManager->getRepository(Friendship::class)->findBy([
            'status' => 'accepted',
        ]);

        $userById = [];
        $currentUserId = (int) $currentUser->getId();
        $userById[$currentUserId] = $currentUser;

        foreach ($friendships as $friendship) {
            $sender = $friendship->getSender();
            $receiver = $friendship->getReceiver();

            if (!$sender || !$receiver || $sender->getId() === null || $receiver->getId() === null) {
                continue;
            }

            $senderId = (int) $sender->getId();
            $receiverId = (int) $receiver->getId();

            if ($senderId === $currentUserId) {
                $userById[$receiverId] = $receiver;
            }

            if ($receiverId === $currentUserId) {
                $userById[$senderId] = $sender;
            }
        }

        return array_values($userById);
    }

    private function normalizeTargetCo2Kg(?float $targetCo2Raw): float
    {
        $value = (float) ($targetCo2Raw ?? 0.0);
        if ($value <= 0) {
            return 2.0;
        }

        // Legacy compatibility: historical records can store kg values directly.
        if ($value <= 50.0) {
            return $value;
        }

        return $value / 1000.0;
    }

    private function scoreFromDailyCo2(float $dailyCo2Kg, float $targetCo2Kg): int
    {
        if ($targetCo2Kg <= 0) {
            return 0;
        }

        $ratio = $dailyCo2Kg / $targetCo2Kg;

        if ($ratio <= 1) {
            $score = 100 + 20 * (1 - $ratio);
            return max(0, (int) round($score));
        }

        $score = 100 - 80 * ($ratio - 1);

        return max(0, (int) round($score));
    }

    private function entryPoints(Entry $entry, float $targetCo2Kg): int
    {
        $value = $entry->getValue();
        if ($value !== null) {
            return max(0, (int) round($value));
        }

        return $this->scoreFromDailyCo2((float) ($entry->getTotalCo2() ?? 0.0), $targetCo2Kg);
    }
}
