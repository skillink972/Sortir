<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function findOneByUsername(string $username): ?Participant
    {
        $qb = $this->createQueryBuilder('p')
            ->orWhere('p.pseudo = :username')
            ->setParameter('username', $username);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
