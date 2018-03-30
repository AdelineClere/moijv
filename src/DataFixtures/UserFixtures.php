<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 60; $i++){    // boucle pr instancier 60 joueurs
            $user = new User();
            $user->setUsername('user_'. $i); // concatène ac $i pour assurer l'unicité
            $user->setPassword(password_hash('user', PASSWORD_BCRYPT));
            $user->setEmail('user'.$i.'fake.fr');
            $user->setRegisterDate(new \DateTime('-'.$i.' days'));   
            $user->setRoles('ROLE_USER');
            $this->addReference('user'.$i, $user);   // on va dire que mon user sera référencé
            // addReference garde en ref notre $user ss un certain nom de façon 
            // à le rendre dispo ds les autres fixtures
            // Datetime = class php qui permet de gérer les dates
            // ' - $i jrs ' pr pas que nos 60, inscrits en même tps
            $manager->persist($user); 
            // $manager persist demande à doctrine de préparer l'insertion de l'entité en BDD 
            // -> INSERT INTO !
        }
        $admin = new User();
        $admin->setUsername('root');
        $admin->setPassword(password_hash('admin', PASSWORD_BCRYPT));   // !! crypter mdp !!
        $admin->setEmail('admin@mail.fr');
        $admin->setRegisterDate(new \DateTime('now'));
        $admin->setRoles('ROLE_USER|ROLE_ADMIN');
        $manager->persist($admin); 
        // tt ce qu'on vient de déclarer il l'enregistre en BDD (=persist)
        
        $manager->flush();
        // flush() valide les req SQL et les exécute
    }
}
