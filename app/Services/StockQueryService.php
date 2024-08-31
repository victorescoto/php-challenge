<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\StockQuery;
use Doctrine\ORM\EntityManager;

class StockQueryService
{
    public function __construct(
        private EntityManager $em,
        private UserService $userService
    ) {}

    public function create(string $stockCode, array $data, int $userId): StockQuery
    {
        $user = $this->userService->get($userId);

        $data['date'] = sprintf('%sT%sZ', $data['date'], $data['time']);
        unset($data['time'], $data['volume']);

        $stockCode = strtoupper($stockCode);

        $stockQuery = new StockQuery($stockCode, $data, $user);

        $this->em->persist($stockQuery);
        $this->em->flush();

        return $stockQuery;
    }

    public function getUserHistory(int $userId): ?array
    {
        return $this->em->getRepository(StockQuery::class)->findBy(
            criteria: ['user' => $userId],
            orderBy: ['createdAt' => 'DESC']
        );
    }
}
