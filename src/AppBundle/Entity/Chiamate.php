<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Chiamate
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Chiamate
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
     * @ORM\Column(name="feedback", type="text", nullable=true)
     */
    private $feedback;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inizio_chiamata", type="datetime")
     */
    private $inizioChiamata;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fine_chiamata", type="datetime")
     */
    private $fineChiamata;

    /**
     * @ORM\ManyToOne(targetEntity="Operatori", inversedBy="chiamate")
     * @ORM\JoinColumn(name="operatore_id", referencedColumnName="id")
     */
    protected $operatore;

    /**
     * @ORM\ManyToOne(targetEntity="Utenti", inversedBy="chiamate")
     * @ORM\JoinColumn(name="utente_id", referencedColumnName="id")
     */
    protected $utente;


    /**
     * @var string
     *
     * @ORM\Column(name="statusUtente")
     */
    private $statusUtente;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="dataRichiamare")
     */
    private $dataRichiamare;

    public function __construct()
    {
        $this->utenti= new Utenti();
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
     * Set feedback
     *
     * @param string $feedback
     *
     * @return Chiamate
     */
    public function setFeedback($feedback=null)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set inizioChiamata
     *
     * @param \DateTime $inizioChiamata
     *
     * @return Chiamate
     */
    public function setInizioChiamata($inizioChiamata)
    {
        $this->inizioChiamata = $inizioChiamata;

        return $this;
    }

    /**
     * Get inizioChiamata
     *
     * @return \DateTime
     */
    public function getInizioChiamata()
    {
        return $this->inizioChiamata;
    }

    /**
     * Set fineChiamata
     *
     * @param \DateTime $fineChiamata
     *
     * @return Chiamate
     */
    public function setFineChiamata($fineChiamata)
    {
        $this->fineChiamata = $fineChiamata;

        return $this;
    }

    /**
     * Get fineChiamata
     *
     * @return \DateTime
     */
    public function getFineChiamata()
    {
        return $this->fineChiamata;
    }

    /**
     * Set operatore
     *
     * @param \AppBundle\Entity\Operatori $operatore
     *
     * @return Chiamate
     */
    public function setOperatore(\AppBundle\Entity\Operatori $operatore = null)
    {
        $this->operatore = $operatore;

        return $this;
    }

    /**
     * Get operatore
     *
     * @return \AppBundle\Entity\Operatori
     */
    public function getOperatore()
    {
        return $this->operatore;
    }

    /**
     * Set utente
     *
     * @param \AppBundle\Entity\Utenti $utente
     *
     * @return Chiamate
     */
    public function setUtente(\AppBundle\Entity\Utenti $utente = null)
    {
        $this->utente = $utente;

        return $this;
    }

    /**
     * Get utente
     *
     * @return \AppBundle\Entity\Utenti
     */
    public function getUtente()
    {
        return $this->utente;
    }




    /**
     * Set statusUtente
     *
     * @param string $statusUtente
     *
     * @return Chiamate
     */
    public function setStatusUtente($statusUtente=null)
    {
        $this->statusUtente = $statusUtente;

        return $this;
    }

    /**
     * Get statusUtente
     *
     * @return string
     */
    public function getStatusUtente()
    {
        return $this->statusUtente;
    }

    /**
     * Set dataRichiamare
     *
     * @param string $dataRichiamare
     *
     * @return Chiamate
     */
    public function setDataRichiamare($dataRichiamare=null)
    {
        $this->dataRichiamare = $dataRichiamare;

        return $this;
    }

    /**
     * Get dataRichiamare
     *
     * @return string
     */
    public function getDataRichiamare()
    {
        return $this->dataRichiamare;
    }

    /**
     * Add chiamate
     *
     * @param \AppBundle\Entity\Utenti $chiamate
     *
     * @return Chiamate
     */
    public function addChiamate(\AppBundle\Entity\Utenti $chiamate)
    {
        $this->chiamate[] = $chiamate;

        return $this;
    }

    /**
     * Remove chiamate
     *
     * @param \AppBundle\Entity\Utenti $chiamate
     */
    public function removeChiamate(\AppBundle\Entity\Utenti $chiamate)
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
     * Add utenti
     *
     * @param \AppBundle\Entity\Utenti $utenti
     *
     * @return Chiamate
     */
    public function addUtenti(\AppBundle\Entity\Utenti $utenti)
    {
        $this->utenti[] = $utenti;

        return $this;
    }

    /**
     * Remove utenti
     *
     * @param \AppBundle\Entity\Utenti $utenti
     */
    public function removeUtenti(\AppBundle\Entity\Utenti $utenti)
    {
        $this->utenti->removeElement($utenti);
    }

    /**
     * Get utenti
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtenti()
    {
        return $this->utenti;
    }
}
