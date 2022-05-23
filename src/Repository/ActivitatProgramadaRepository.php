<?php

namespace App\Repository;

use App\Entity\ActivitatProgramada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivitatProgramada>
 *
 * @method ActivitatProgramada|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivitatProgramada|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivitatProgramada[]    findAll()
 * @method ActivitatProgramada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivitatProgramadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivitatProgramada::class);
    }

    public function add(ActivitatProgramada $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ActivitatProgramada $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ActivitatProgramada[] Returns an array of ActivitatProgramada objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActivitatProgramada
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
