<?php

namespace App\Controller;

use App\Repository\ProductRepository;
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
    
    //1ere route, par défaut : début de pdts
    // 2e = qd on a choisi sur quelle pg aller..
    // Route permet d'exécuter fct index de mon Controller,
    // ds cette fct j'ut. 1 ou pls modèles (ProductRepository) pour récup données qui seront transmises à ma vue 
    /**
     * @Route("/home", name="home")
     * @Route("/home/{page}", name="home_paginated")
     */
    public function index(ProductRepository $productRepo, $page = 1)  
    // 2 choses injectées en param : ProductRepository = c une injection de dépendances
    // et $page qui lui est issu de l'url (injecté de l'url)
    // !! on peut injecter que élts venues de src (et encore pas toutes)(cf. Config/services.yalm = 
    // précisent les class que l'on peut injecter ds Controllers, autres class aussi
    // à l'url home, cette fct° sera exécutée ; si on passe par home, 1ère pg sera = à 1
    {          
        $products = $productRepo->findPaginated($page); //je récup ma liste de pdts/pg (avec pg choisie)       
        return $this->render("home.html.twig", [
            'products' => $products
        ]);
        // Dc a récup list user ac findAll et on transfère à notre vue ac render :
        // on appelle le home.html.twig : on ajoute du contenu à Home + )
        // Dessine-moi ce template (home.html.twig) ac la variable msg qui vaudra message
    }       
}
