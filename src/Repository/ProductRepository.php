<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }
    
    public function findPaginated($page = 1)    // le pager passé ebn param de la fct
    {
        $queryBuilder = $this->createQueryBuilder('p')->orderBy('p.id', 'ASC'); 
// On créé le queryBuilder (rappel alias en req. sql (table membre m ...)
        $pager = new DoctrineORMAdapter($queryBuilder);      
// obligé de passer par un objet Adapteur pr faire lien entre le query builder de doctrine et le pager fanta, créé, now le paginer ac pagerFanta
        $fanta = new \Pagerfanta\Pagerfanta($pager);    
// l'objet fanta permet de définir quelle est pg courante, à partir de ça on peut créer le pager fanta
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);   
// on lui passe en argument la pg, on aura plus qu'à transmettre à la vue
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
