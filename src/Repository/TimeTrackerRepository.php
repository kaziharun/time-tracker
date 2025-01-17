<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeTracker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeTracker>
 */
class TimeTrackerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeTracker::class);
    }

    /** @return array<TimeTracker> */
    public function findByUser(int $userId): array
    {
        return $this->findBy(
            ['user' => $userId],
            ['startDate' => 'ASC', 'startTime' => 'ASC']
        );
    }

    /** @return array<TimeTracker> */
    public function findOverlappingEntries(
        \DateTimeInterface $startDate,
        \DateTimeInterface $startTime,
        ?\DateTimeInterface $endTime,
        int $userId
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.startDate = :startDate')
            ->andWhere('t.startTime < :endTime')
            ->andWhere('t.endTime > :startTime')
            ->setParameter('user', $userId)
            ->setParameter('startDate', $startDate)
            ->setParameter('startTime', $startTime);

        if ($endTime) {
            $qb->setParameter('endTime', $endTime);
        } else {
            $qb->setParameter('endTime', $startTime);
        }

        /** @var array<TimeTracker> $timeTracker */
        $timeTracker = $qb->getQuery()->getResult();

        return $timeTracker;
    }
}
