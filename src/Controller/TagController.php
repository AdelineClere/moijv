<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


// annot° pr tte la pg
/**
 * @Route("/tag")
 */
class TagController extends Controller
{
        // Cette route déclenchée par choix du tag par user 
        // > va appeler template du tag en question de tag/index.html.twig
        //  (.. va tenter un get by slug et va trouver un tag)
    /**
     * @Route("/{slug}/product", name="tag")   
     * @Route("/{slug}/product/{page}", name="tag_paginated")    
     */
    public function product(ProductRepository $productRepo, Tag $tag, $page = 1) 
    // injecter en params le ProductRepository pour récup pdts, le tag, la pg = = Cr récup et utilise
    // $tag = slug 'transformé' en nom du tag / $page -> car il y aura pls pg de pdts liés à 1 tag..
    // on va appeler les pdts liés à ce tag qu'on a trouvé grâce au slug ds url
    {     
        // je récup la pg. ProductRepository nous sert à faire des modèles
        // et cette pg on va la transmettre à findPaginated
        $tagProductListT = $productRepo->findPaginatedByTag($tag, $page);
        // transmettre cette liste de pdts récup à la vue, on l'appelle 'products'
        
        return $this->render('tag/product.html.twig', [  
            'tag' => $tag,
            'products' => $tagProductListT
        ]);
        
    }
        
        

}



 
    