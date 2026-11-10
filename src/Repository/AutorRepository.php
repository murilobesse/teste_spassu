<?php

namespace App\Repository;

use App\Entity\Autor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Autor>
 */
class AutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autor::class);
    }

    public function findByNome(
        ?string $nome = null,
        string $sort = 'codau',
        string $order = 'desc',
    ): \Doctrine\ORM\QueryBuilder {
        $qb = $this->createQueryBuilder('a');

        $allowedSorts = ['codau' => 'a.CodAu', 'nome' => 'a.nome'];
        $sortField = $allowedSorts[strtolower($sort)] ?? 'a.CodAu';

        switch (strtolower($order)) {
            case 'asc':
                $order = \SortDirection::Ascending;
                break;
            case 'desc':
                $order = \SortDirection::Descending;
                break;
            default:
                $order = \SortDirection::Descending;
        }

        if ($nome) {
            $qb->andWhere('LOWER(a.nome) LIKE LOWER(:nome)')
            ->setParameter('nome', "%$nome%");
        }
        $qb->addOrderBy($sortField, $order);

        return $qb;
    }

    //    /**
    //     * @return Autor[] Returns an array of Autor objects
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

    //    public function findOneBySomeField($value): ?Autor
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
