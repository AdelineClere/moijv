<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        
        for($i = 0; $i < 40; $i++){    // boucle pr instancier 40 pdts
            $product = new Product();
            $product->setTitle('produit_'. $i); // concatène ac $i pour assurer l'unicité
            $product->setDescription();

            $manager->persist($product); 
            // $manager persist demande à doctrine de préparer l'insertion de l'entité en BDD 
            // -> INSERT INTO !
        }    
        $manager->flush();
        // flush() valide les req SQL et les exécute
    }
}


   