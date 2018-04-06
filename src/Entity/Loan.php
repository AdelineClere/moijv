<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
Use Doctrine\ORM\Mapping\Table;

    // je veux que le statut soit indexé (tablo indexé) > rendre indexable la propriété (le status) (cf lg 10) > créer un index
    // // rq. : qd on fait recher sur plusieurs col. > on fait un index
    // on declare (avec {}) une list d'index à notre table, on lui donne nom : staus_idx, 1 col status
    // on pourrait aussi écrire : @ORM\Table
/**
 * @ORM\Entity(repositoryClass="App\Repository\LoanRepository")
 * @Table(indexes={@Index(name="staus_idx", columns={"status"})}))
 */
class Loan
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_add;

    /**
     *
     * @var type @ORM\Column(type="datetime", nullable=true)
     */
    private $date_end;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status;
    
    
            // ac ManyToOne > inversedBy
            // ici on met en rel° loaner ac le user qui a le pdt
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="loans")
     * @var User
     */
    private $loaner;
 
            // 1 pdt = 1 emprunt => $var Product
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="loans")
     * @var Product
     */
    private $product;
    

    public function getId()
    {
        return $this->id;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getDateEnd()
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
    
    public function getLoaner()
    {
        return $this->loaner;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setLoaner(User $loaner) 
    {
        $this->loaner = $loaner;
        return $this;
    }

    public function setProduct(Product $product) 
    {
        $this->product = $product;
        return $this;
    }


    
    
    
    
    
}
