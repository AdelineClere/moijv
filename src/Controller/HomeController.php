<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @route("/",name="root")  // si après login ne trouve pas de route de redirection
     */
    public function root()  // on exéc cette fct
    {
        return $this->redirectToRoute('home');  // qui redirige vers home
    }
    /**
     * @Route("/home", name="home")
     */
    public function index(UserRepository $userRepo)  // à l'url home, cette fct° sera exécutée :
    {   // ns on va faire en sorte que notre appli return du HTML
        
        $userList = $userRepo->findAll();     
        
        return $this->render("home.html.twig", ['users' => $userList]); 
        // Dc a récup list user ac findAll et on transfère à notre vue ac render :
        // on appelle le home.html.twig : on ajoute du contenu à Home + )
        // Dessine-moi ce template (home.html.twig) ac la variable msg qui vaudra message
    }       
}
