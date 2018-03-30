<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    // on a créé cette route (1ere lg) qui transmet cette pg :
    // {page} = param dynamiq, on récupère ce qui correspond à ça dessous : 
    // je récup pg en argument de ma fct :
    
    /**
     * @Route("/product", name="product")   
     * @Route("/product/{page}", name="product_paginated", requirements={"page"="\d+"})  
     */  
    public function index(ProductRepository $productRepo, $page = 1)    
    // je récup la pg. ProductRepository nous sert à faire des modèles
    {   // et cette pg on va la transmettre à findPaginated
        $productList = $productRepo->findPaginated($page);
        
        return $this->render('product/index.html.twig', [   // aller ds ce fichier
            'products' => $productList    // = products correspondra à $productList
        ]);
    }
    
    /**
     * @Route("/product/delete/{id}", name="delete_product")   
     */
    public function deleteProduct(Product $product, ObjectManager $manager)
    //  avt ctrl shift i : public function deleteProduct(Product $produit, \Doctrine\Common\Persistence\ObjectManager $manager)
    // (...) = j'écris la class Product pour aller chercher mon pdt OK / je donne nom à ma var : $product
    // Je vais avoir besoin de mon ObjectManag.. (m'écrit tt) OK -> je l'appelle $manager
    {       
        $manager->remove($product);
        $manager->flush();
        return $this->redirectToRoute('product');   
        // redirect attend un nom de route (mieux que chemin) / direct attend un chemin
       
    }
    /**
     * @Route("/product/add", name="add_product")
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function editProduct(Request $request, ObjectManager $manager, Product $product = null)
    {   // request = permet de faire en OO ce qu'on faisait ac superglobales en procédural
        // ici on passe request direct ds formulaire qui va s'en occuper
        // fct sera acces via 2 routes différentes (cf. au dessus) 
        if($product === null) {  // pas de pdt > lg60, 63, 65 > 71 : j'aff form ac erreurs
            $product = new Product();   
        }
        
        $formProduct = $this->createForm(ProductType::class, $product)  // crée form
                ->add('Envoyer', SubmitType::class);
        
        $formProduct->handleRequest($request);  
        // dit de prendre en cpte ce qui a été envoyé en post, get etc. et de valider
        
        if($formProduct->isSubmitted() && $formProduct->isValid()) {    
        // si pas rentré 1er if > si form valide, je =le sauvegarde et redirige (sinon réaff. form)
            $manager->persist($product);
            $manager->flush();      // je nettoie
            return $this->redirectToRoute('product');               // je redirige
        }
        
        // moment à partir duquel on est rentré ds le formulaire (renseigné et renvoyé) :  
        return $this->render('product/edit_product.html.twig', [     // aff. form
            'form' => $formProduct->createView()
        ]);
        
    }
    
    
    
}
