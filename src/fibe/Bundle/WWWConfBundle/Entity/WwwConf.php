<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\SecurityBundle\Entity\User;

/**
 * WwwConf
 *
 * @ORM\Entity
 * @ORM\Table(name="conference") 
 */
class WwwConf
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
     * @ORM\Column(name="confName", type="string", length=255)
     */
    private $confName;

    /**
     * @var string
     *
     * @ORM\Column(name="confUri", type="string", length=255,nullable=true)
     */
    private $confUri;

    /**
     * @var string
     *
     * @ORM\Column(name="confOwlUri", type="string", length=255,nullable=true)
     */
    private $confOwlUri;


    /**
    * confEvents
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\ConfEvent", mappedBy="wwwConf",cascade={"persist", "remove"})
    */
    private $confEvents; 

    /**
    * confManager
    *
    * @ORM\OneToMany(targetEntity="fibe\SecurityBundle\Entity\User", mappedBy="wwwConf",cascade={"persist"})
    */
    private $confManagers;
    
    public function __toString() 
    {
        return $this->confName;
    }
    
    public function getId()
    {
        return $this->id;
    } 
    

    /**
     * confEvents
     */
     
    public function addConfManager(\fibe\SecurityBundle\Entity\User $confManager = null)
    {
        $this->confManagers[] = $confManager;
    
        return $this;
    }
    
    public function removeConfManager(\fibe\SecurityBundle\Entity\User $confManager)
    {
        $this->confManagers->removeElement($confManager);
    }
    
    public function getConfManagers()
    {
        return $this->confManagers;
    }
    
    
    public function setConfOwlUri($ConfOwlUri)
    {
        $this->confOwlUri = $ConfOwlUri;
    
        return $this;
    } 
    
    public function getConfOwlUri()
    {
        return $this->confOwlUri;
    }
    
    
    
    public function setConfUri($ConfUri)
    {
        $this->confUri = $ConfUri;
    
        return $this;
    } 
    
    public function getConfUri()
    {
        return $this->confUri;
    }
    
    
    
    
    public function setConfName($ConfName)
    {
        $this->confName = $ConfName;
    
        return $this;
    } 
    
    public function getConfName()
    {
        return $this->confName;
    }
    
    

    /**
     * confEvents
     */
     
    public function addConfEvents(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $confEvent)
    {
        $this->confEvents[] = $confEvent;
    
        return $this;
    }
    
    public function removeConfEvents(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $confEvent)
    {
        $this->confEvents->removeElement($confEvent);
    }
    
    public function getConfEvents()
    {
        return $this->confEvents;
    }
    
}
