<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
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
    
    /**
     * @Route("/admin/user/delete/{id}", name="delete_user")
     */
    public function deleteUser(User $user, ObjectManager $manager)  // qd on commence à taper Obj... > longue lg > ctrl shift i
    {
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('admin_dashboard');
    }
    
    
    
    
}
