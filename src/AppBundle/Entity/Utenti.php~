<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * utenti
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Utenti
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=200)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone", type="integer")
     */
    private $phone;

     /**
     * @var string
     *
     * @ORM\Column(name="campagna", type="string", length=200)
     */
    private $campagna;


    /**
    * 
    * @ORM\OneToMany(targetEntity="Chiamate", mappedBy="utenti")
    */
    protected $chiamate;
    

    public function __construct()
    {   
        
        $this->chiamate=new ArrayCollection();

    }

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return utenti
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return utenti
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return utenti
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return utenti
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Add utente
     *
     * @param \AppBundle\Entity\Chiamate $utente
     *
     * @return Utenti
     */
    public function addUtente(\AppBundle\Entity\Chiamate $utente)
    {
        $this->utente[] = $utente;

        return $this;
    }

    /**
     * Remove utente
     *
     * @param \AppBundle\Entity\Chiamate $utente
     */
    public function removeUtente(\AppBundle\Entity\Chiamate $utente)
    {
        $this->utente->removeElement($utente);
    }

    /**
     * Get utente
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtente()
    {
        return $this->utente;
    }

    /**
     * Add chiamate
     *
     * @param \AppBundle\Entity\Chiamate $chiamate
     *
     * @return Utenti
     */
    public function addChiamate(\AppBundle\Entity\Chiamate $chiamate)
    {
        $this->chiamate[] = $chiamate;

        return $this;
    }

    /**
     * Remove chiamate
     *
     * @param \AppBundle\Entity\Chiamate $chiamate
     */
    public function removeChiamate(\AppBundle\Entity\Chiamate $chiamate)
    {
        $this->chiamate->removeElement($chiamate);
    }

    /**
     * Get chiamate
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChiamate()
    {
        return $this->chiamate;
    }

    /**
     * Set campagna
     *
     * @param string $campagna
     *
     * @return Utenti
     */
    public function setCampagna($campagna)
    {
        $this->campagna = $campagna;

        return $this;
    }

    /**
     * Get campagna
     *
     * @return string
     */
    public function getCampagna()
    {
        return $this->campagna;
    }

    /**
     * Set chiamata
     *
     * @param \AppBundle\Entity\Chiamate $chiamata
     *
     * @return Utenti
     */
    public function setChiamata(\AppBundle\Entity\Chiamate $chiamata = null)
    {
        $this->chiamata = $chiamata;

        return $this;
    }

    /**
     * Get chiamata
     *
     * @return \AppBundle\Entity\Chiamate
     */
    public function getChiamata()
    {
        return $this->chiamata;
    }
}
