<?php

namespace App\Repository;

use App\Entity\EDT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Utilisateur;

/**
 * @extends ServiceEntityRepository<EDT>
 *
 * @method EDT|null find($id, $lockMode = null, $lockVersion = null)
 * @method EDT|null findOneBy(array $criteria, array $orderBy = null)
 * @method EDT[]    findAll()
 * @method EDT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EDTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EDT::class);
    }
    
    public function findAllByDate(\DateTime $date, Utilisateur $medecin) {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT e FROM App\Entity\EDT e
                WHERE e.date = :date AND e.id_medecin = :medecin'
        )
                ->setParameter('date', $date)
                ->setParameter('medecin', $medecin);
        
        
        return $query->getResult();
    }

//    /**
//     * @return EDT[] Returns an array of EDT objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EDT
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
