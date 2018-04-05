<?php

namespace App\DataTransformers;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;


class TagTransformer implements DataTransformerInterface
{
        // pour utiliser un repository dans un service on ne peut que l'injecter dans 
        // le constructeur de ce service. Alors que dans les controller on peut les injecter 
        // dans n importe quelle methode.
        // On crée un var privée $tagRepo pour pvr l'utiliser ds ttes les fct°
        // On l'instancie ds __construct dessous 
    /**
     *
     * @var TagRepository
     */
    private $tagRepo;

    public function __construct(TagRepository $tagRepo) // on récup TagRepository
    {
        $this->tagRepo = $tagRepo;  
    }
   
    // transform list nom en collec de tags, exéc qd formulaire renvoie données pr les sauvegarder
    public function reverseTransform($tagString) // 
    {
        $tagArray = array_unique(explode(',', $tagString)); // on trasform string en array
        $tagCollection = new ArrayCollection(); //ArrayCollection sera réinjecté dans notre pdt mais var privée 
            // => faire un setter pour tag ds Product.php
            // à partir de ce tablo de noms on va chercher tags correspondant en bd, 
            // on en fait une collec, pour les rassigner au pdt 
        
        foreach($tagArray as $tagName) {
            $tag = $this->tagRepo->getCorrespondingTag($tagName);
            $tagCollection->add($tag);
        }
        return $tagCollection; 
            // retourne $tagCollection utililsée ensuite par doctrine pour ê réassignée à mes pdts
    }

    
    
    public function transform($tagCollection) // étape au moment de mettre tag ds formuilaire (= avt que form soit affiché
            // pbl car collection contient des tags et slugs, pas des noms => la transforme en string (ac noms séparés par , )
    {
            // array_map (function php) sert à transformer mes tags en noms de tag
        $tagArray = $tagCollection->toArray(); // on transforme coll° en array pour array_map
            // on lui passe la fct de conversion qui transforme tag en nom de tag 
            // (associe un nom à tag) en callback (=sous-fct°) l'array
            // 
            // 2e argt : on appliq sur chacun des élts du tablo la boucle array_map qui va leur appliquer la fct :
        $nameArray = array_map(function($tag){ return $tag->getName(); }, $tagArray); // on a récup tablo de noms
//          foreach ($tagArray as $tag)
//          {
//            $nameArray[] = $tag->getName();
//          }
        return implode(',', $nameArray); // on implode pour récup string des noms de tag
    }

}
