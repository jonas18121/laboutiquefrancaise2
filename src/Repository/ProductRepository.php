<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Cherche des produits en fonction de la recherche de l'user
     *
     * @param Search $search
     * @return Product[]|null
     */
    public function findWithSearch(Search $search)
    {
        $query = $this->createQueryBuilder('p') // le mapping est fait sur la table Product
            ->select('c', 'p')
            ->join('p.category', 'c');

        // Si l'user a choisi une ou plusieurs catégorie depuis la checkbox, on l'affiche
        if (!empty($search->getCategories())) {
            $query = $query
            ->andWhere('c.id IN (:searchCategories)')
            ->setParameter('searchCategories', $search->getCategories());
        }

        
        if (!empty($search->getString())) {
            $query = $query

            // Si l'user a écrit le nom d'un produit depuis l'input, on l'affiche
            ->orWhere('p.name LIKE :searchName')
            ->setParameter('searchName', "%{$search->getString()}%") // La recherche est partielle donc, 
            //si on ecrit "bon", on va afficher tous les produits qui contiennent "bon"

            // Si l'user a écrit le prix d'un produit depuis l'input, on l'affiche
            ->orWhere('p.price LIKE :searchPrice')
            ->setParameter('searchPrice', "%{$search->getString()}%");
        }

        // dd($query->getQuery()->getResult());

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
