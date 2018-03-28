<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    
    public function index(UserRepository $userRepo)  // à l'url home, cette fct° sera exécutée :
    {   // ns on va faire en sorte que notre appli return du HTML
        
        $userList = $userRepo->findAll(); 
        // $userRepo est passé automtqt en paramètre par Sfy
        // -> injection de dépendances. On a dc pas à l'instancier ns-même.
        // $userRepo effectuera ici un SELECT * FROM user ...
        
        // Dc a récup list user ac findAll et on transfère à notre vue ac render :
        return $this->render("admin/dashboard.html.twig", [     // penser à changer home > admin/dasboard...
            'users' => $userList
        ]); 
        // Dessine-moi ce template (dashboard.html.twig) ac la variable msg qui vaudra message
    }       
}
