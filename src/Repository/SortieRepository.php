<?php

namespace App\Repository;

use App\Entity\PropertySearch;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;
use function Symfony\Component\String\s;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère les sorties en lien avec une recherche
     * @return Sortie[]
     */
    public function findSearch(PropertySearch $search) : array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s');


        if ($search->getCampus()) {
            $query = $query
                ->innerJoin('s.campus', 'c')
                ->andWhere('c.nom LIKE :campus')
                ->setParameter('campus', $search->getCampus()->getNom());
        }

        if ($search->getMotCle()!==null) {
            $query = $query
                ->andWhere('s.nom LIKE :motCle')
                ->setParameter('motCle', "%{$search->getMotCle()}%");
        }

        if (!empty($search->getDateMin())) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $search->getDateMin());
        }

        if (!empty($search->getDateMax())) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $search->getDateMax());
        }

        if ($search->isOrganisateur()) {
            $query1 = $query
                ->innerJoin('s.organisateur', 'o')
                ->andWhere('o.id LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
        }
        else {
            $query1 = $query
                ->innerJoin('s.organisateur', 'o')
                ->andWhere('o.id NOT LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
        }
//
//        if ($search->isInscrit()) {
//            $query2 = $query
//                ->innerJoin('s.participants', 'pi')
//                ->andWhere('pi.id LIKE :user')
//                ->setParameter('user', $search->getUser()->getId());
//        }
//        else {
//            $query2 = $query
//                ->innerJoin('s.participants', 'pi')
//                ->andWhere('pi.id NOT LIKE :user')
//                ->setParameter('user', $search->getUser()->getId());
//        }
//
//        if ($search->isNonInscrit()) {
//            $query3 = $query
//                ->innerJoin('s.participants', 'pni')
//                ->andWhere('pni.id NOT LIKE :user')
//                ->setParameter('user', '6');
//        }
//        else {
//            $query3 = $query
//                ->innerJoin('s.participants', 'pni')
//                ->andWhere('pni.id LIKE :user')
//                ->setParameter('user', '6');
//        }
//
        if ($search->isPassees()) {
            $query4 = $query
                ->innerJoin('s.etat', 'e')
                ->andWhere('e.id = 5');
        }
        else {
            $query4 = $query
                ->innerJoin('s.etat', 'e')
                ->andWhere('e.id != 5');
        }

//       + $query2->getQuery()->getResult() + $query3->getQuery()->getResult()
        $allQueries = $query->getQuery()->getResult() + $query1->getQuery()->getResult() + $query4->getQuery()->getResult();
        return $allQueries ;
    }


//    /**

//     * @return Sortie[] Returns an array of Sortie objects

//     * @return SortieController[] Returns an array of SortieController objects

//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


//    public function findOneBySomeField($value): ?Sortie

//    public function findOneBySomeField($value): ?SortieController

//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
