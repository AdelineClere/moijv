<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
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
    
    public function findPaginated($page = 1)    // le pager passé en param de la fct
    {
        $queryBuilder = $this->createQueryBuilder('p') // p vient de product récup par _construct au dessus
            ->orderBy('p.id', 'ASC'); 
// On créé le queryBuilder (rappel alias en req. sql (table membre m ...)
        $pager = new DoctrineORMAdapter($queryBuilder);      
// obligé de passer par un objet Adapteur pr faire lien entre le query builder de doctrine et le pager fanta, créé, now le paginer ac pagerFanta
        $fanta = new Pagerfanta($pager);    
// l'objet fanta permet de définir quelle est pg courante, à partir de ça on peut créer le pager fanta
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);   
// on lui passe en argument la pg, on aura plus qu'à transmettre à la vue
    }

    public function findPaginatedByUser (User $user, $page = 1) // 'User' class du namespace app/entity => ctrl shift i
    // fct qui prend en param un $user de la class user et en 2è param une page
    // pour afficher que les pdts de l'user
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.owner', 'u') // je cherche à joindre le proprio du pdt = leftJoin prend 2 param : p.owner et u (u fait ref à p.owner
// leftJoin = si pas de user corresp tu prends qd même le pdt / rightJoin = si pas de pdt tu prends qd même user 
// innerJoin = tu prends que qd corresp user-pft            
// si ds entity pdt j'ai un owner, c'est bien le proprio et je donne allias u et u doit ê = à user
            ->where('u = :user')  // on met un marqueur, (ou ? = marqueur qui a pas de nom)
            ->setParameter('user', $user)   // = bindParam
            ->orderBy('p.id', 'ASC'); 
        $pager = new DoctrineORMAdapter($queryBuilder);      
        $fanta = new Pagerfanta($pager);    
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);   
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
