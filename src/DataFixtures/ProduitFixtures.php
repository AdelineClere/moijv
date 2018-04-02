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
        // $product = new Product();        (code d'ex. par défaut)
        // $manager->persist($product);
        
        for($i = 0; $i < 150; $i++){    // boucle pr instancier 40 pdts
            $product = new Product();   // ctrl shift i > a raccourci le ' \App\Entity8Product'
            $product->setTitle('Mon produit n°'. $i); // concatène ac $i pour assurer l'unicité
            $product->setDescription("Description de mon produit n°$i");
            
            $product->setOwner($this->getReference('user' . rand(0, 59)));
            // attribuer un pdtFixture à un user au hasard ds les 60 
            
            $manager->persist($product); 
            // $manager persist demande à doctrine de préparer l'insertion de l'entité en BDD 
            // -> INSERT INTO !
        }    
        
        $manager->flush();
        // flush() valide les req SQL et les exécute
    }
    
    public function getDependencies(): array  
    // pdtFixture ne peut pas s'exécuter avant userFixture or c ce qui va se passer si on ne fait rien
    {
        return [
           UserFixtures::class 
        ];
    }
}


   