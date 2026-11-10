<?php

namespace App\Repository;

use App\Entity\Assunto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SortDirection;

/**
 * @extends ServiceEntityRepository<Assunto>
 */
class AssuntoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assunto::class);
    }

    public function findByDescricao(
        ?string $descricao = null, 
        string $sort = 'codau', 
        string $order = 'desc'
    ): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        $allowedSorts = ['codau' => 'a.CodAs', 'nome' => 'a.nome'];
        $sortField = $allowedSorts[strtolower($sort)] ?? 'a.CodAs';
        
        switch (strtolower($order)) {
            case 'asc':
                $order = SortDirection::Ascending;
                break;
            case 'desc':
                $order = SortDirection::Descending;
                break;
            default:
                $order = SortDirection::Descending;
        }

        if ($descricao) {
            $qb->andWhere('LOWER(a.descricao) LIKE LOWER(:descricao)')
            ->setParameter('descricao', "%$descricao%");
        }

        $qb->addOrderBy($sortField, $order);

        return $qb;
    }

//    /**
//     * @return Assunto[] Returns an array of Assunto objects
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

//    public function findOneBySomeField($value): ?Assunto
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
