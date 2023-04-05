<?php

namespace App\Repository;

use App\Entity\PropertySearch;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * Requête de base : filtres campus, mot clé, date min et date max
     * @param PropertySearch $search
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getBuilder(PropertySearch $search): \Doctrine\ORM\QueryBuilder
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

        if ($search->getMotCle() !== null) {
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
        return $query;
    }

    public function findAccueil(PropertySearch $search): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.campus', 'c')
            ->andWhere('c.nom LIKE :campus')
            ->setParameter('campus', $search->getCampus()->getNom());

        $q = $this
            ->createQueryBuilder('se')
            ->select('se');

        $qb = $q
            ->innerJoin('s.etat', 'e')
            ->andWhere('e.id = 5');

        $query = $query
            ->andWhere($query->expr()->notIn('s',$qb->getDQL()));

        return $query->getQuery()->getResult();
    }


    /**
     * Requête liée aux sorties dont je suis l'organisateur/rice
     * @return Sortie[]
     */
    public function findSearch1(PropertySearch $search) : array
    {
        $query = $this->getBuilder($search);

        if ($search->isOrganisateur()) {
            $query = $query
                ->innerJoin('s.organisateur', 'o')
                ->andWhere('o.id LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
        }

        return $query->getQuery()->getResult() ;
    }

    /**
     * Requête liée aux sorties auxquelles je suis inscrit/e
     * @return Sortie[]
     */
    public function findSearch2(PropertySearch $search) : array
    {
        $query = $this->getBuilder($search);

        if ($search->isInscrit()) {
            $query = $query
                ->innerJoin('s.participants', 'pi')
                ->andWhere('pi.id LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Requête liée aux sorties auxquelles je ne suis pas inscrit/e
     * @return Sortie[]
     */
    public function findSearch3(PropertySearch $search) : array
    {
        $query = $this->getBuilder($search);

        if ($search->isNonInscrit()) {
            $qb = $this
                ->createQueryBuilder('s1')
                ->select('s1');
            $q = $qb
                ->innerJoin('s1.participants', 'pi')
                ->andWhere('pi.id LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
            $qb2 = $this
                ->createQueryBuilder('s2')
                ->select('s2');
            $q2 = $qb2
                ->innerJoin('s2.etat', 'e')
                ->andWhere('e.id = 5');
            $qb3 = $this
                ->createQueryBuilder('s3')
                ->select('s3');
            $q3 = $qb3
                ->innerJoin('s3.organisateur', 'o')
                ->andWhere('o.id LIKE :user')
                ->setParameter('user', $search->getUser()->getId());
            $query = $query
                ->andWhere($query->expr()->notIn('s',$q->getDQL()))
                ->andWhere($query->expr()->notIn('s',$q2->getDQL()))
                ->andWhere($query->expr()->notIn('s',$q3->getDQL()))
                ->setParameter('user', $search->getUser());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Requête liée aux sorties passées
     * @return Sortie[]
     */
    public function findSearch4(PropertySearch $search) : array
    {
        $query = $this->getBuilder($search);

        if ($search->isPassees()) {
            $query = $query
                ->innerJoin('s.etat', 'e')
                ->andWhere('e.id = 5');
        }

        return $query->getQuery()->getResult();
    }


}
