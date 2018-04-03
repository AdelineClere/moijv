<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

// la l'unique entity c'est pour l'application (au dessous = infos pour l'appli)
/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("title")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

  
    // la unique=true >> pour la BDD !!
    /**
     * @ORM\Column(type="string", length=80, unique=true)
     * @Assert\Length(min=3, max=50)
     */
    private $title;

     /**
     * @ORM\Column(type="text")
     * @assert\Length(min=15, max=65000)
     */
    private $description;
    
    
    // @Assert\Img = attend une img
    // @Assert\NotBlank(groups={"insertion"}) = qd on est en mode insertion il faut un fichier (pas blank)
    // ( car on veut que le notBlank ce soit que en mode insertion, pas edition !!! )
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"insertion"})
     * @Assert\Image(maxSize = "2M", minWidth="200", minHeight="200")
     * @var object
     */
    private $image;
    

    // ma var est un User et elle s'app ManyToOne
    // doctrine attend nom du champ ds Entity
    // on dit à ORM doctrine que target entity c User
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
     * @var User owner
     */
    private $owner;
    
    
    // on vise entity/tag, on choisi nom qu'on donnera : products (car 1 tag aura pls pdts)
    // C une collec
    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="products")
     * @var Collection
     */
    private $tags;
    
    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
    
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function getTags(): Collection {
        return $this->tags;
    }

    public function setTags(Collection $tags) {
        $this->tags = $tags;
        return $this;
    }


    
    
    
}
