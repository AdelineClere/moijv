<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Tag;
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
            // $queryBuilder = sert à construire une req, on le stock ds 1 var car avt de devenir req 
            // c'est 1 objet comm tt en Sfy ==> chainages de méthodes 
        $queryBuilder = $this->createQueryBuilder('p') // p vient de product récup par _construct au dessus
                ->leftJoin('p.owner', 'u')      // = p.owner_id = u.id (on cherch si user courant est proprio...
                ->addSelect('u')                // u = owner (nimporte quel proprio, puisq on cherche commun ac user courant 
                ->leftJoin('p.tags', 't')  
                ->addSelect ('t') 
                ->leftJoin('p.loans', 'l')              // pr n'appeler que les pdts dispos
                ->where('l.status = :status1')
                ->orWhere('l.status = :status2')
                ->orWhere('l.status is NULL')
                ->setParameter('status1', 'finished')    
                ->setParameter('status2', 'refused')
                ->orderBy('p.id', 'DESC');              // DESC => envie ptetre de voir pdts + récents...
                
            // On créé le queryBuilder (rappel alias en req. sql (table membre m ...)
        $pager = new DoctrineORMAdapter($queryBuilder);      
            // obligé de passer par un objet Adapteur pr faire lien entre le queryBuilder de doctrine 
            // et le pager fanta, créé, now le paginer ac pagerFanta
        $fanta = new Pagerfanta($pager);    
            // l'objet fanta permet de définir quelle est pg courante, à partir de ça on peut créer le pager fanta
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);   
            // on lui passe en argument la pg, on aura plus qu'à transmettre à la vue
    }

    
        // fct qui prend en param un $user de la class user et en 2è param une page
        // pour afficher que les pdts de l'user
    public function findPaginatedByUser (User $user, $page = 1) // pg qui récup pdts d'1 utlisateur
    // 'User' class du namespace app/entity => ctrl shift i
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.owner', 'u') // récup tt (cf schémas jointures...)
                // je cherche à joindre le proprio du pdt = leftJoin = tu prends que qd corresp user-pdt 
                // si ds entity pdt j'ai un owner, c'est bien le proprio et je donne alias u et u doit ê = à user
                // leftJoin prend 2 param : p.owner et u (u fait ref à p.owner), si pas de user corresp tu prends qd même le pdt / 
                // rightJoin = si pas de pdt tu prends qd même user               
            ->addSelect('u')    // = comm si je rajoutais SELECT * / sél TOUTES les données des users
                //(en coulisse par doctrine <=> SELECT p.*, u.* FROM product INNER JOIN user ON p.user_id = u.id
            
            ->leftJoin('p.tags', 't')       // jointure entre tags et tags des pdts tagués qui doivent ê communs
            ->addSelect ('t')               // rajouter à ma requete mes tags
                                            // ici on affiche corresp ac user commun
            ->where('u = :user')            //sur 1 objet ici : $user / (on met marqueur ou ? = marqueur qui a pas de nom)
            ->setParameter('user', $user)   //= bindParam / En sql : user_id = 7 (cf. Queries ds profiler de Sfy)
            ->orderBy('p.id', 'ASC');      
    
        $pager = new DoctrineORMAdapter($queryBuilder);      
        $fanta = new Pagerfanta($pager);    
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);       
    }
    
    
        // +- Faire un getProduct des pdts corresp au tag
    public function findPaginatedByTag (Tag $tag, $page = 1) 
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.owner', 'u')
            ->addSelect('u')
            ->leftJoin('p.tags', 't')  
            ->leftJoin('p.tags', 't2')  
            ->addSelect ('t')          
            ->where('t2 = :tag') 
            ->leftJoin('p.loans', 'l')  // pr n'appeler que les pdts dispos 
            ->setParameter('tag', $tag) // tag_id = 7
            ->orderBy('p.id', 'DESC'); 
        
        $orGroup = $queryBuilder->expr()->orX();   // Besoin de ça dès qu'on aura besoin de gérer des ()
        $orGroup->add($queryBuilder->expr()->eq('l.status', ':status1'));
        $orGroup->add($queryBuilder->expr()->eq('l.status', ':status2'));
        $orGroup->add($queryBuilder->expr()->isNull('l.status'));
      
        $queryBuilder->andWhere($orGroup)
            ->setParameter('status1', 'refused')
            ->setParameter('status2', 'finished');
   
        $pager = new DoctrineORMAdapter($queryBuilder);      
        $fanta = new Pagerfanta($pager);    
        return $fanta->setMaxPerPage(12)->setCurrentPage($page);       
    }
    
            // 2 leftJoin car (on emprunte 2 fois la table.. ?:
            // 1er > on veut afficher les pdts associés au tag MAIS AUSSI les tags de ces pdts
            // 2e  > faire le filtre : je filtre sur t2 
                // leftJoin user u
                // leftJoin tag t
                // leftJoin tag t2
                // WHERE t2.id = 7

    
    
}
