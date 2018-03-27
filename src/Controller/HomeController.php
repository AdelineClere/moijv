<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function index() // à l'url home, cette fct° sera exécutée :
    {   // ns on va faire en sorte que notre appli return du HTML
        
        $message ='Bonjour à tous'; // je vais transmettre msg a mon template
        return $this->render("home.html.twig", ['msg' => $message]); 
        // on appelle le home.html.twig : on ajoute du contenu à Home + )
        // Dessine-moi ce template (home.html.twig) ac la variable msg qui vaudra message
    }       
}
