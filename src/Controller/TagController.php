<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\ProductRepository;
use App\Repository\TagRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


// annot° pr tte la pg
/**
 * @Route("/tag")
 */
class TagController extends Controller
{
        // Cette route déclenchée par choix du tag par user 
        // Qd clic sur un lien, user declenche l'url /tag/{le slug de notre produit}/product
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
    // fct search qui repond à une route : chemin en fait, de type : /tag?search
    /**
     * @Route("", name="search_tag")
     */
    public function search(TagRepository $tagRepo, Request $request)   // va rép du json
    // injecter Repository en param et app recherche ac request, qu'on injecte aussi
    {
        $search = $request->query->get('search');  // ici on récup la recherche dans l'url
            //$_GET['search'] en procédural / search => liste de mots
            // app tagRepo et passer en param ces mots 
            // -> créer new fct searchBySlug ds TagRepository    
        if(! $search) {
            throw $this->createNotFoundException(); // si pas de param search on lui fait croire qu'on a pas trouvé la pg
        }
        $slugify = new Slugify();   // slugify la liste de mots / On converti la rech en slug :
        $slug = $slugify->slugify($search);  // on a transform la rech en slug & on fait rech sur le slug
        $searchTags = $tagRepo->searchBySlug($slug);    // retourne un tablo
            // on prend notre Repo injecté + haut et app fct searchBySlug 
            // on a resultat de la rech ss form d'une coll°
            // on a fait une recherche de tags grâce aux slug
            // on converti cette coll° de tags en tablo php MAIS php pas cap de transformer tablo tag en json dc prendre coll tag et le trasnform en tablo
        $formatedTagsArray = []; // je déclare la var
        foreach($searchTags as $tag) {       // je parcours mon searchTagArray
        $formatedTagsArray[] = ['name' => $tag->getName(), 'slug' => $tag->getSlug()];           
        }
        return $this->json($formatedTagsArray);  // on a les tags en json qui corresp bien aux slugs demandés
        // on va exploité ça ds notre script selectize edlit_product.html.twig
                
// convertir aussi les slugs en tablo : 
    
                
    }
}



 
    