<?php

namespace App\Repository;

use App\Entity\Tag;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class); // ce repository se réfère à class Tag
    }

    
    // méthode pour récup, à partir d'un nom de tag choisi par le user trouver le tag en BDD
    // @param : lu par php doc et netbeans, ... (pr auto-complétion par ex.)
    // @return > un tag corresp à celui en db ou un new tag
    /**
     * 
     * @param type $tagName the name of the tag we are looking for
     * @return Tag the matching tag in db or a new Tag instance if no corresponding tag is found
     */
    public function getCorrespondingTag($tagName) 
    // on va récup le slug pas le nom -> une librairie pour 'slugifier'
    {
        $slugify = new Slugify();
        $tagSlug = $slugify->slugify($tagName);
            // Slugify = objet qui transforme truc en slug ac méthode slugify 
            // (il prends $tagName) > retourne $tagSlug
        $tag = $this->findOneBy(['slug' => $tagSlug]); // je récup un tag ou pas, et le créé sinon
            // je demande à mon repository un (seul) tag corresp au slug généré
            // en sql = SELECT t.* FROM tag as t WHERE slug = :tagSlug LIMIT 1 
            // 
            // 
        // tester si tag existe ou pas, créer un sinon
        if( ! $tag) {
            $tag = new Tag();
            $tag->setName($tagName);
            $tag->setSlug($tagSlug);
        }
        return $tag;
    }
    
    
    
    
    
    
//    /**
//     * @return Tag[] Returns an array of Tag objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
   
}
