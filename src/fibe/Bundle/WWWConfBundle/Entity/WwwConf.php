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
    * Keywords
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Keyword", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $keywords;

    /**
    * Keywords
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Organization", mappedBy="conference",cascade={"persist", "remove"})
    */
    private $organizations;

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
    

    /**
    * Mobile app configurations
    *
    * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig",cascade={"persist"})
    * @ORM\JoinColumn(name="appConfig", referencedColumnName="id")
    */
    private $appConfig;

    /**
    * @var string
    *
    * @ORM\Column(name="logo", type="string", length=255,nullable=true)
    */
    private $logo;


    /**
    * @var string
    *
    * @ORM\Column(name="acronym", type="string", length=255,nullable=true)
    */
    private $acronym;

    /**
     * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\ConfEvent", cascade={"persist"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     **/
     private $mainConfEvent;


    
    public function __toString() 
    {
        return $this->mainConfEvent->getSummary();
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
    

    public function setLogo($Logo)
    {
        $this->logo = $Logo;
    
        return $this;
    } 
    
    public function getLogo()
    {
        return $this->logo;
    }


    public function setAcronym($Acronym)
    {
        $this->acronym = $Acronym;
        return $this;
    } 
    
    public function getAcronym()
    {
        return $this->acronym;
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

    /**
     * Add app config
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig $AppConfig
     * @return WwwConf
     */
    public function setAppConfig($AppConfig)
    {
        $this->appConfig = $AppConfig;
    
        return $this;
    } 
    
    public function getAppConfig()
    {
        return $this->appConfig;
    }

    

    /**
     * Add keywords
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords
     * @return WwwConf
     */
    public function addKeyword(\fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords)
    {
        $this->keywords[] = $keywords;
    
        return $this;
    }

    /**
     * Remove keywords
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords
     */
    public function removeKeyword(\fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords)
    {
        $this->keywords->removeElement($keywords);
    }

    /**
     * Get keywords
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Add organizations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
     * @return WwwConf
     */
    public function addOrganization(\fibe\Bundle\WWWConfBundle\Entity\Organization $organizations)
    {
        $this->organizations[] = $organizations;
    
        return $this;
    }

    /**
     * Remove organizations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
     */
    public function removeOrganization(\fibe\Bundle\WWWConfBundle\Entity\Organization $organizations)
    {
        $this->organizations->removeElement($organizations);
    }

    /**
     * Get organizations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * Set mainConfEvent
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $mainConfEvent
     * @return WwwConf
     */
    public function setMainConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $mainConfEvent = null)
    {
        $this->mainConfEvent = $mainConfEvent;
    
        return $this;
    }

    /**
     * Get mainConfEvent
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\ConfEvent 
     */
    public function getMainConfEvent()
    {
        return $this->mainConfEvent;
    }
}