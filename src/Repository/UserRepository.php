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

    public function findOneByEmailOrUsername(string $emailOrUsername): ?Participant
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.email = :emailOrUsername')
            ->orWhere('p.pseudo = :emailOrUsername')
            ->setParameter('emailOrUsername', $emailOrUsername);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
