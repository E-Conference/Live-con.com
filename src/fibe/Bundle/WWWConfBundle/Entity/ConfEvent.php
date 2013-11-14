<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

use IDCI\Bundle\SimpleScheduleBundle\Entity\Event; 
use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\Paper;


/**
 * ConfEvent
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="IDCI\Bundle\SimpleScheduleBundle\Repository\EventRepository")
 */
class ConfEvent extends Event
{
    
    /**
     * slidePresentation
     * Url to slides presentation     
     *
     * @ORM\Column(type="string", nullable=true,  name="slidePresentation")
     */
    protected $slidePresentation;

    /**
     * conference
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="events", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

    /**
     * @ORM\ManyToMany(targetEntity="Paper", inversedBy="events", cascade={"persist"})
     * @ORM\JoinTable(name="confEvent_paper",
     *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id")})
     */
    private $papers;

    /**
     * @ORM\ManyToMany(targetEntity="Topic", inversedBy="events", cascade={"persist"})
     * @ORM\JoinTable(name="confEvent_topic",
     *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")})
     */
    private $topics;


    /**
     * roles
     * Persons related to an event 
     *  
     * @ORM\OneToMany(targetEntity="Role", mappedBy="event",cascade={"persist","remove"})
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $roles;


    /**
     *  
     * Is an all day event 
     * Used for ui representation in the calendar view
     *   
     * @ORM\Column(name="is_allday", type="boolean")
     */
     private $isAllDay ;


    /**
     *  
     * Is a main conf event ?
     *   
     * @ORM\Column(name="is_mainConfEvent", type="boolean")
     */
     private $isMainConfEvent = false;
     
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->topics = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->xProperties = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /** computeIsAllDay
     *
     * this is only computed on creation for the importer
     *  @ORM\PrePersist() 
     */
    public function computeIsAllDay()
    {      
        $start = $this->getStartAt();
        $end = $this->getEndAt(); 
        $this->setIsAllDay($start->format('d')!=$end->format('d'));
    }
    
    /**
     * Set slidePresentation
     *
     * @param string $slidePresentation
     * @return ConfEvent
     */
    public function setSlidePresentation($slidePresentation)
    {
        $this->slidePresentation = $slidePresentation;
    
        return $this;
    }

    /**
     * Get slidePresentation
     *
     * @return string 
     */
    public function getSlidePresentation()
    {
        return $this->slidePresentation;
    }

    
    /**
     * Set url
     *
     * @param string $url
     * @return ConfEvent
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
   
    /**
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return ConfEvent
     */
    public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference)
    {
        $this->conference = $conference;
    
        return $this;
    }

    /**
     * Get conference
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf 
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * Add papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     * @return ConfEvent
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
     * Add topics
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
     * @return ConfEvent
     */
    public function addTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
    {
        $this->topics[] = $topics;
    
        return $this;
    }

    /**
     * Remove topics
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
     */
    public function removeTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
    {
        $this->topics->removeElement($topics);
    }

    /**
     * Get topics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Add roles
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Role $roles
     * @return ConfEvent
     */
    public function addRole(\fibe\Bundle\WWWConfBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Role $roles
     */
    public function removeRole(\fibe\Bundle\WWWConfBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    
    /**
     * Set isAllDay
     *
     * @param string $isAllDay
     * @return ConfEvent
     */
    public function setIsAllDay($isAllDay)
    {
        $this->isAllDay = $isAllDay;
    
        return $this;
    }

    /**
     * Get isAllDay
     *
     * @return string 
     */
    public function getIsAllDay()
    {
        return $this->isAllDay;
    }

    
    /**
     * Set isMainConfEvent
     *
     * @param string $isMainConfEvent
     * @return ConfEvent
     */
    public function setIsMainConfEvent($isMainConfEvent)
    {
        $this->isMainConfEvent = $isMainConfEvent;
    
        return $this;
    }

    /**
     * Get isMainConfEvent
     *
     * @return string 
     */
    public function getIsMainConfEvent()
    {
        return $this->isMainConfEvent;
    }

    
}