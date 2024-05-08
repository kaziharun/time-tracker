<?php

namespace App\Repository;

use App\Entity\TimeTracker;
use App\Entity\User;
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

    public function findByUser(int $userId): array
    {
        return $this->findBy(
            ['user' => $userId],
            ['startDate' => 'ASC', 'startTime' => 'ASC']
        );
    }

    public function findOverlappingEntries(\DateTimeInterface $startDate, \DateTimeInterface $startTime, \DateTimeInterface $endTime, int $userId): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->where('t.user = :user')
            ->andWhere('t.startDate = :startDate')
            ->andWhere('t.startTime < :endTime')
            ->andWhere('t.endTime > :startTime')
            ->setParameter('user', $userId)
            ->setParameter('startDate', $startDate)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }
}
