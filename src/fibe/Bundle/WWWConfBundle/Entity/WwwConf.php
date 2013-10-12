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
    * locations
    *
    * @ORM\OneToMany(targetEntity="IDCI\Bundle\SimpleScheduleBundle\Entity\Location", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $locations; 

    /**
    * Papers
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Paper", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $papers;

    /**
    * Persons
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Person", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $persons;

    /**
    * Themes
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Theme", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $themes;

    /**
    * confManager
    *
    * @ORM\ManyToMany(targetEntity="fibe\SecurityBundle\Entity\User", mappedBy="conferences",cascade={"persist"})
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
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->confEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->confManagers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add confEvents
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents
     * @return WwwConf
     */
    public function addConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents)
    {
        $this->confEvents[] = $confEvents;
    
        return $this;
    }

    /**
     * Remove confEvents
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents
     */
    public function removeConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents)
    {
        $this->confEvents->removeElement($confEvents);
    }

    /**
     * Add locations
     *
     * @param \IDCI\Bundle\SimpleScheduleBundle\Entity\Location $locations
     * @return WwwConf
     */
    public function addLocation(\IDCI\Bundle\SimpleScheduleBundle\Entity\Location $locations)
    {
        $this->locations[] = $locations;
    
        return $this;
    }

    /**
     * Remove locations
     *
     * @param \IDCI\Bundle\SimpleScheduleBundle\Entity\Location $locations
     */
    public function removeLocation(\IDCI\Bundle\SimpleScheduleBundle\Entity\Location $locations)
    {
        $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Add papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     * @return WwwConf
     */
    public function addPaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
    {
        $this->papers[] = $papers;
    
        return $this;
    }

    /**
     * Remove papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     */
    public function removePaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
    {
        $this->papers->removeElement($papers);
    }

    /**
     * Get papers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPapers()
    {
        return $this->papers;
    }

    /**
     * Add persons
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $persons
     * @return WwwConf
     */
    public function addPerson(\fibe\Bundle\WWWConfBundle\Entity\Person $persons)
    {
        $this->persons[] = $persons;
    
        return $this;
    }

    /**
     * Remove persons
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $persons
     */
    public function removePerson(\fibe\Bundle\WWWConfBundle\Entity\Person $persons)
    {
        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Add themes
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Theme $themes
     * @return WwwConf
     */
    public function addTheme(\fibe\Bundle\WWWConfBundle\Entity\Theme $themes)
    {
        $this->themes[] = $themes;
    
        return $this;
    }

    /**
     * Remove themes
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Theme $themes
     */
    public function removeTheme(\fibe\Bundle\WWWConfBundle\Entity\Theme $themes)
    {
        $this->themes->removeElement($themes);
    }

    /**
     * Get themes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThemes()
    {
        return $this->themes;
    }
}