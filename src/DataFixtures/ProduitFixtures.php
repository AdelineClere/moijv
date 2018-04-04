<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        for($i = 0; $i < 150; $i++){    // boucle pr instancier 40 pdts
            $product = new Product();   // ctrl shift i > a raccourci le ' \App\Entity\Product'
            $product->setTitle('Mon produit n°'. $i); // concatène ac $i pour assurer l'unicité
            $product->setDescription("Description de mon produit n°$i");
            $product->setImage("uploads/500x325.png");  
            $product->setOwner($this->getReference('user' . rand(0, 59)));
            // attribuer un ProduitFixtures à un user au hasard ds les 60 
            
            // Associer plusieurs tags à un produit :
            for($j = 0; $j < rand(0,4); $j++) {   // tag qui vient de TagFixtures (grâce à addReference)
                $tag = $this->getReference ('tag' . rand(0, 39));   
                // !! rand peut renvoyer 2 fois le même tag !! -> utiliser méthode addTag ds Product.php 
                // à chq boucle on récup 1 entité tag pour 1 pdt ($product), 0 à 4 fois 
                // (Rq : en POO chaq objet est une ref)
                $product->addTag($tag);  // on utilise méthode addTag créée dans Product
                // = ajoute tag au pdt et pdt au tag + évite doublon
            }
            $manager->persist($product); 
            // $manager persist demande à doctrine de préparer l'insertion de l'entité en BDD 
            // -> INSERT INTO !
        }    
        
        $manager->flush();
        // flush() valide les req SQL et les exécute
    }
    
    public function getDependencies(): array  
    // getDep... est une méthode de DependentFixtureInterface (+ haut)
    // -> elle a en dépendance UserFixtures, 
    // pdtFixture ne peut pas s'exécuter avant userFixture or c ce qui va se passer si on ne fait rien
    {
        return [
            UserFixtures::class, 
            TagFixtures::Class  
            // qd on va faire fixtures load > vont s'exéc par ordre alphab et on essayer de récup 
            // une réf à un user pas encore référencé 
            // > attendre que UserFixtures et TagFixtures se soient exécutés'
        ];
    }
}


   