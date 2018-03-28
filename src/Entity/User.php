<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    // on veut que username soit unique : on rajoute unique=true
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
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
    public function setRoles($roles) 
    {
        $this->roles = $roles;
    }
 
    
    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRegisterDate(): \DateTimeInterface
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

}
