<?php

namespace App\Repository;

use App\Entity\Livro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SortDirection;

/**
 * @extends ServiceEntityRepository<Livro>
 */
class LivroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    public function findByTitulo(
        ?string $titulo = null, 
        string $sort = 'codl', 
        string $order = 'asc'
    ): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->createQueryBuilder('l');

        $allowedSorts = [
            'codl' => 'l.codl',
            'titulo' => 'l.titulo',
            'editora' => 'l.editora',
            'edicao' => 'l.edicao',
            'anopublicacao' => 'l.anoPublicacao',
            'valor' => 'l.valor',
        ];
        $sortField = $allowedSorts[strtolower($sort)] ?? 'l.codl';
        
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

        if ($titulo) {
            $qb->andWhere('LOWER(l.titulo) LIKE LOWER(:titulo)')
            ->setParameter('titulo', "%$titulo%");
        }

        $qb->addOrderBy($sortField, $order);

        return $qb;
    }

//    /**
//     * @return Livro[] Returns an array of Livro objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livro
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
