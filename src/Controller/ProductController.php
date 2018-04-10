<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


    // on rajoute une annot° valable pour tt notre controller = il gèrera tt ce qui commence par product
    // (on peut suppr ds annot° suivantes le ' product '
    // Ttes ces Routes seront réservées à un utlisateur qui a le rôle user 
    // = page pour utlisateur pour SES pdts = son espace de gestion perso
/**
 * @Route("/product")
 */
class ProductController extends Controller
{
        // on a créé cette route (1ere lg) qui transmet cette pg :
        // {page} = param dynamiq, on récupère ce qui correspond à ça dessous : 
        // je récup pg en argument de ma fct :  
    /**
     * @Route("/", name="product")   
     * @Route("/{page}", name="product_paginated", requirements={"page"="\d+"})  
     */  
    public function index(ProductRepository $productRepo, $page = 1)    
        // ProductRepository est injecté en param = le C. le récup & l'utilise pr faire des modèles
        // je récup la pg. on va la transmettre à findPaginated
    { 
        $productList = $productRepo->findPaginatedByUser($this->getUser(), $page);
        
        return $this->render('product/index.html.twig', [   // envoyer à cette vue
            'products' => $productList    // = products correspondra à $productList
        ]);
    } 
    
    
    
     
        // Product = Entité => pas injectable par voie classiq = vecteur d'injection url 
        // ObjectManager = vecteur d'injection de Sfy : par dépendances
    /**
     * @Route("/delete/{id}", name="delete_product")   
     */
    public function deleteProduct(Product $product, ObjectManager $manager)
    //  avt ctrl shift i : ....(Product $produit, \Doctrine\Common\Persistence\ObjectManager $manager)
    // (...) = j'écris la class Product pour aller chercher mon pdt OK / je donne nom à ma var : $product
    // Je vais avoir besoin de mon ObjectManag.. (m'écrit tt) OK -> je l'appelle $manager
    {     
        if($product->getOwner()->getId() !== $this->getUser()->getId()){ 
        // si proprio du pdt a id ≠ de id user courant alors :
        // req getOwner pas faite mais doctrine le fait automt.
            throw $this->createAccessDeniedException('You are not allowed to delete this product');
        }
        $manager->remove($product); // suppr de BDD
        $manager->flush();          // exécute et nettoie (efface)
        return $this->redirectToRoute('product');   
        // redirect attend un nom de route (mieux que chemin) / direct attend un chemin
    }
    
    
      
    
    
        // product/add = route pour ajouter un pdt
        // sinon en mode edit -> insertion
    /**
     * @Route("/add", name="add_product")   
     * @Route("/edit/{id}", name="edit_product")
     */
    public function editProduct(Request $request, ObjectManager $manager, Product $product = null)
    {   // request = permet de faire en OO ce qu'on faisait ac superglobales en procédural
        // ici on passe request direct ds formulaire qui va s'en occuper
        // fct sera acces via 2 routes différentes (cf. au dessus) 
        if($product === null) {  // pas de pdt > lg60, 63, 65 > 71 : j'aff form ac erreurs
            $product = new Product();   
            $group ='insertion';
        } else {
            $oldImage = $product->getImage();             
            $product->setImage(new File($product->getImage()));
            // je transforme getImage (qui est chaine de caract) en fichier File 
            // avant de le passer ($product) à mon formulaire dessous :
            $group ='edition';
        }
    
// !! à ce niveau ici j'ai soit un new pdt soit un qui existe en bdd
 
// je créé un formulaire de type ProductType qu'on nomme $product
        $formProduct = $this->createForm(ProductType::class, $product, ['validation_groups'=>[$group]])  
                ->add('Envoyer', SubmitType::class);
 
// ici on dit à notre formul de gérer la requête : il va prendre pdt passé au dessus et modifié ses champs (à l'int va changer)
        $formProduct->handleRequest($request);  // handleRequest qui fait ça
        // dit de prendre en cpte ce qui a été envoyé en post, requête, get etc. et de valider
        // en mode edition si on a rien modifié >>> il met null sur File (img) CAR File est un fichier de type non prérempli !!!
        // pour titre et descript. -> pré-rempli > dc écrase et remplace par ce qui était déjà !!
        
        if($formProduct->isSubmitted() && $formProduct->isValid()) {    
        // si pas rentré 1er if > si form valide, je le sauvegarde et redirige (sinon réaff. form)    
            $product->setOwner($this->getUser());  //= le prorio du pdt c l'user qui est connecté
            $image = $product->getImage();
            if ($image === null) {   // j'ai edit, modifié txt, mais pas img => aller récup img ki est en DB 
                $product->setImage($oldImage);
            } else {      
            $newFileName = md5(uniqid()) . '.' . $image->guessExtension(); 
            // newFN contient mon md5 (nom crypté pour avoir nom uniq si pls users chargent img de même nom img) + l'extension
            $image->move('uploads', $newFileName); 
            // ac move (= méthode de File) : je déplace mon fichier ds doss upload en le renommant newFileName
            // = change local° du fichier img : 1er param = la où je mets fichier / 2è param = nom qu'on lui donne
            $product->setImage('uploads/'.$newFileName); 
            // repasser objet en chaine 
            // désormais en DB ce sera son chemin et son nom sera ça
            }
            
            $manager->persist($product);    // rentre en BDD 
            $manager->flush();              // exécute et nettoie (efface)
            return $this->redirectToRoute('product');   // je redirige vers la route 'Product)  
        }   
        
        // si formul pas soumis ou pas valide
        // moment à partir duquel on est rentré ds le formulaire (renseigné et renvoyé) :  
        return $this->render('product/edit_product.html.twig', [     // aff. form de nouveau
            'form' => $formProduct->createView()
        ]);
        
    }
    

}
// afficher que list pdt de user