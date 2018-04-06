<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


// peut pas y avoir 2x même email ni même username :
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")   
 * @UniqueEntity("username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    // on veut que username soit unique : on rajoute unique=true
    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Length(min=2, max=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=50)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registerDate;

    
    /**
     *@ORM\Column(type="string", length=100)
     */
    private $roles;
    

    
        // on crée une var products qui contiendra ts les pdts de l'utilisateur
        // var = annotation de php. > devra gén. getter & setter pour ce genre de class (Coll° products)
        // one to many = 1 user aura pls pdts.
        // dans Entity Product > mettre nom du champ où un propriétaire
    /**  
     * @ORM\OneToMany(targetEntity="Product", mappedBy="owner")
     * @var Collection products
     */
    private $products;
    
    
        // targetEntity => mettre le nom de la class visée   
        // mappedBy = nom qui a la propriété dans l'entité visée (cf > Loan.php 
        // Coll° (= tablo orienté objet) car pls
    /**
     * @ORM\OneToMany(targetEntity="Loan", mappedBy="loaner")
     * @var Collection
     */
    private $loans;
    
    // $this... car ce st des collec
    public function __construct() {
       $this->products = new ArrayCollection();
       $this->loans = new ArrayCollection();
    }
    
    public function getProducts(): Collection 
    {
        return $this->products;
    }

     
    public function setRoles($roles) 
    {
        $this->roles = $roles;
    }
 
    
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail() 
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    public function setRegisterDate(\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
        return explode('|', $this->roles);
    }

    public function getSalt() {
        return null;
    }

    public function serialize(): string {
         return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below / y'a pas de grain de sel
            // $this->salt,
        ));
    }
    
    public function unserialize($serialized) {
         list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
    }
        // on peut garder car c une collec
    public function getLoans(): Collection {
        return $this->loans;
    }

    public function setLoans(Collection $loans) {
        $this->loans = $loans;
        return $this;
    }

 
    
    
    
    
    
    
    
    
    
    
    
    
    
}
