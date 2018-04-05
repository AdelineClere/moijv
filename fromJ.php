                                TagRepository
<?php

namespace App\DataTransformers;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{
    
    /**
     *
     * @var TagRepository
     */
    private $tagRepo;
    
    // le Model du MVC se decompose en 2 : ce qui sert a represneter les données(entity, et tt ce qui concerne la bdd), et TOUT CE QUI N EST PAS UTILISE PAR LA BDD(les services) . en plus des 2, on a: (les fixtures et les migration servent simplement dans le developpement) 
    public function __construct(TagRepository $tagRepo)
    {
        // pour utiliser un repository dans un service on ne peut que l'injecter dans le constructeur de ce service. Alors que dans les controller on peut les injecter dnas n importe quelle methode.
        //plug-inn = paquet= programme
        //librairie :on peut utiliser les elements ou pas. Framework : on doit travailler avec les elements
        $this->tagRepo = $tagRepo;
        
    }
    
    public function reverseTransform($tagString) {
        //explode: chaine->Array   ->collection de tags
        
        $tagArray =explode(',', $tagString);
        //on crée un nouveau Tableau, orienté Objet, qui sera appelé $tagCollection:
        $tagCollection = New ArrayCollection();
        foreach ($tagArray as $tagName) {
            $tagCollection->add($this->tagRepo->getCorrespondingTag($tagName));
        }
        return $tagCollection;
        

    //mr wong modele lenovo modele ideapad tm110 usb3.0 mopf9xb7326073 110-15acl 20volt 2.25A 80tj chargeur 23€ lundi ;
    }

    public function transform($tagCollection) {
        //collection de tags -> Array -> chaine
        //implode: Array->chaine
        
        //array_map(function, array)
       $tagArray= $tagCollection->toArray();
       $nameArray= array_map(function($tag){return $tag->getName();},$tagArray);
       //foreach($tagArray as $tag)
       //{
       //   $nameArray[]=$tag->getName();
       //}
        return implode(',',$nameArray);
    }

}

                                TagController
<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("/tag")
 */
class TagController extends Controller
{
    /**
     * //En cliquant sur un lien, l'user declenchera l'url /tag/{le slug de notre produit}/product
     * //path pointe sur LE NOM DE LA ROUTE
     * @Route("/{slug}/product", name="tag")
     * // '/product' a ete rajoute car generalement on ne tag pas que des pdts! on tag aussi des user...
     * @Route("/{slug}/product/{page}", name="tag_paginated")
     */
    public function product(ProductRepository $productRepo, Tag $tag, $page=1)
    {
        //ici, par rapport a productController, on a importe en plus, dans function product,  Tag $tag afin d'etre en adequation avec productRepository
        $tagProductList=$productRepo->findPaginatedByTag($tag,$page);
        //ici, findPaginatedByTag contient, en parametre $tag au lieu de $this->getTag(). $tag c'est parcequ'on recupere son slug DANS L'URL. $this->getUser() permet de recuperer l'user DANS LA SESSION 
        
        return $this->render('tag/product.html.twig', [
            'tagProducts' => $tagProductList,
            'tag'=>$tag
            //on veut passer notre vue a ce tag car a priori on va ensuite faire une requete
        ]);
    }
}


                                Tag.php

<?php

namespace App\Entity;    //pour la bdd. ex le cp:  entier
// pour la vue. ex; le cp: 5 caracteres


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("title")// "unique coté application (pareil que pour l'exemple du cp
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)//"unique" coté bdd (idem que ex du cp)
     * @Assert\Length(min=3, max=50)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=15)
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", length=255)
     * //la ligne du dessous permet de controler, avant insertion, que l'user cherche bien a inserer une image
     * //cette regle ne s applique qu'en cas d insertion. NotBlank="pas vide"! il faut metre quelqu chose.
     * //je ne veut obliger(par le biais de NotBlank) mon user a choisir une image qu'en cas d'insertion: Si l'user modifier son produit, on lui imposera pas de réuploader sa photo.
     * //assert c est les contraintes. Asset est le nom que l'on donne a notre dossier , contenant les fichiers js,image...
     * //le champ ne doit pas etre blans,vide si on est en mode insertion. cette restriction ne s'applique qu'en cas d'insertion:
     * @Assert\NotBlank(groups={"insertion"})
     * //tandis que la restiction sur l'image s'applique tout le temps. assert image verifie que c'est une image et aussi que c'est bien un fichier
     * @Assert\Image(
     * maxSize = "2M",
     * minWidth = "200",
     * minHeight = "200"
     * )
     * @var object
     */
    private $image;    
    
    
     /**
     * ci dessous, on declare que notre owner est un objet de type User, (donc de l'entity User)
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products") 
     * @var User owner
     */   
    private $owner;
    
     /**
     * dans mon entité "Tag" les produits s'appeleront "products"
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="products") 
     * @var Collection
     */   // $tags est relié a la classe Tag grace a l'anotation du dessus:targetEntity="Tag"
    private $tags;

    public function __construct()
    {
        // ArrayCollection= un Array orienté objet.
        //arraycollection implement bien la table Collection:voir au dessus "var Collection"
        $this-> tags=new ArrayCollection();
    }



        public function getOwner(): User {
        return $this->owner;
    }

    public function setOwner(User $owner) {
        $this->owner = $owner;
        return $this;
    }

        public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getImage() 
    {
        return $this->image;
    }
    public function setImage($image) 
    {
        $this->image = $image;
        
        return $this;
    }
// make:migration  permet de creer le fichier "version"
//migrations:migrate nous a rajouté une colonne "image" dans notre bdd
    
    public function addTag($tag)
    {
        // fct qui prendra en parametre le tag a ajouter
        // tags est notre collection(voir ligne 69 et 75). contains() est une method de arraycollection et qui implemente Collection
        if($this->tags->contains($tag))
        {//si les tags du produits contienent deja le tag qu'on essaie d'ajouter, alors il sort aussitot de la fct.
            
            return;//le return fait directement sortir de la fct
        }
        //on ajoute le tag a la liste des tags du produit
        // chauqe prdt a des tags. j'ajoute un tag
        $this->tags->add($tag);
        // $tag, provenant de fixutures est passee en parametre. Getproduct vient de la class tag. on lui rajoute this qui est ici un pdt
        $tag->getProducts()->add($this);
    }
    public function getTags(): Collection {
        //exporte la valeur tags et nous retourne une arraycollection tags
        return $this->tags;
    }    
// c'est le if qui permet de se premunir contre les doublons.
    public function setTags(Collection $tags)
    {
        //importe une collection de $tags et fixe la valeur à la variable tags
        $this-> tags= $tags;
        return $this;
    }

}
