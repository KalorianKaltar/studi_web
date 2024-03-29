<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

use \App\Query\CastAsInteger;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @implements PasswordUpgraderInterface<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    
    private function findAllByType($value) {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
          'SELECT u
                FROM App\Entity\Utilisateur u
                    WHERE u.id_type IN (
                        SELECT tu.id
                            FROM App\Entity\TypeUtilisateur tu
                                WHERE tu.label = :label
                    )'      
        )->setParameter('label', $value);
        
        return $query->getResult();
    }
    
    
    public function findAllMedecins() {
        return $this->findAllByType('Médecin');
    }
    
    public function findAllPatients() {
        return $this->findAllByType('Patient');
    }
    
    public function findMaxMatriculeBySpecialite($specialite) {
        $entityManager = $this->getEntityManager();
        
        $config = $entityManager->getConfiguration();
        $config->addCustomNumericFunction('INT', CastAsInteger::class);
        
        $query = $entityManager->createQuery(
          'SELECT MAX(INT(SUBSTRING(u.matricule, 2))) as MAX
                FROM App\Entity\Utilisateur u
                    WHERE u.id_specialite = :specialite'      
        )->setParameter('specialite', $specialite);
        
        $result = $query->setMaxResults(1)->getOneOrNullResult();
        
        return $result['MAX'];
    }
    
//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
