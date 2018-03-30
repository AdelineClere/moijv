<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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

    // ma var est un User et elle s'app ManyToOne
    // doctrine attend nom du champ ds Entity
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
     * @var User owner
     */
    private $owner;
     
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
}
