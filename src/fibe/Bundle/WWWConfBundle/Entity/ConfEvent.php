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
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="IDCI\Bundle\SimpleScheduleBundle\Repository\EventRepository")
 */
class ConfEvent extends Event
{
    
    /**
     * wwwConf
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confEvents", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $wwwConf;


    /**
     * @ORM\ManyToMany(targetEntity="Paper", inversedBy="confEvents", cascade={"persist"})
     * @ORM\JoinTable(name="confEvent_paper",
     *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id", onDelete="Cascade")})
     */
    private $papers;


    /**
     * role
     * Persons related to an event 
     *  
     * @ORM\OneToMany(targetEntity="Role", mappedBy="event")
     */
    private $role;

   
   

 
    /**
     * @var boolean
     */
    private $isTransparent;

    /**
     * @var \DateTime
     */
    private $endAt;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var string
     */
    private $resources;

    /**
     * @var string
     */
    private $duration;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $startAt;

    /**
     * @var \DateTime
     */
    private $lastModifiedAt;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $organizer;

    /**
     * @var integer
     */
    private $revisionSequence;

    /**
     * @var string
     */
    private $contacts;

    /**
     * @var string
     */
    private $excludedDates;

    /**
     * @var string
     */
    private $includedDates;

    /**
     * @var string
     */
    private $classification;

    /**
     * @var \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $xProperties;

    /**
     * @var \fibe\Bundle\WWWConfBundle\Entity\Status
     */
    private $status;

    /**
     * @var \IDCI\Bundle\SimpleScheduleBundle\Entity\Location
     */
    private $location;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \fibe\Bundle\WWWConfBundle\Entity\Recur
     */
    private $includedRule;

    /**
     * @var \fibe\Bundle\WWWConfBundle\Entity\Recur
     */
    private $excludedRule;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->xProperties = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set isTransparent
     *
     * @param boolean $isTransparent
     * @return ConfEvent
     */
    public function setIsTransparent($isTransparent)
    {
        $this->isTransparent = $isTransparent;
    
        return $this;
    }

    /**
     * Get isTransparent
     *
     * @return boolean 
     */
    public function getIsTransparent()
    {
        return $this->isTransparent;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return ConfEvent
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    
        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return ConfEvent
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resources
     *
     * @param string $resources
     * @return ConfEvent
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    
        return $this;
    }

    /**
     * Get resources
     *
     * @return string 
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set duration
     *
     * @param string $duration
     * @return ConfEvent
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    
        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ConfEvent
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return ConfEvent
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    
        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set lastModifiedAt
     *
     * @param \DateTime $lastModifiedAt
     * @return ConfEvent
     */
    public function setLastModifiedAt($lastModifiedAt)
    {
        $this->lastModifiedAt = $lastModifiedAt;
    
        return $this;
    }

    /**
     * Get lastModifiedAt
     *
     * @return \DateTime 
     */
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return ConfEvent
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    
        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ConfEvent
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ConfEvent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
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
     * Set organizer
     *
     * @param string $organizer
     * @return ConfEvent
     */
    public function setOrganizer($organizer)
    {
        $this->organizer = $organizer;
    
        return $this;
    }

    /**
     * Get organizer
     *
     * @return string 
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Set revisionSequence
     *
     * @param integer $revisionSequence
     * @return ConfEvent
     */
    public function setRevisionSequence($revisionSequence)
    {
        $this->revisionSequence = $revisionSequence;
    
        return $this;
    }

    /**
     * Get revisionSequence
     *
     * @return integer 
     */
    public function getRevisionSequence()
    {
        return $this->revisionSequence;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     * @return ConfEvent
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    
        return $this;
    }

    /**
     * Get contacts
     *
     * @return string 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set excludedDates
     *
     * @param string $excludedDates
     * @return ConfEvent
     */
    public function setExcludedDates($excludedDates)
    {
        $this->excludedDates = $excludedDates;
    
        return $this;
    }

    /**
     * Get excludedDates
     *
     * @return string 
     */
    public function getExcludedDates()
    {
        return $this->excludedDates;
    }

    /**
     * Set includedDates
     *
     * @param string $includedDates
     * @return ConfEvent
     */
    public function setIncludedDates($includedDates)
    {
        $this->includedDates = $includedDates;
    
        return $this;
    }

    /**
     * Get includedDates
     *
     * @return string 
     */
    public function getIncludedDates()
    {
        return $this->includedDates;
    }

    /**
     * Set classification
     *
     * @param string $classification
     * @return ConfEvent
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
    
        return $this;
    }

    /**
     * Get classification
     *
     * @return string 
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set wwwConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf
     * @return ConfEvent
     */
    public function setWwwConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf = null)
    {
        $this->wwwConf = $wwwConf;
    
        return $this;
    }

    /**
     * Get wwwConf
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf 
     */
    public function getWwwConf()
    {
        return $this->wwwConf;
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
     * Add role
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Role $role
     * @return ConfEvent
     */
    public function addRole(\fibe\Bundle\WWWConfBundle\Entity\Role $role)
    {
        $this->role[] = $role;
    
        return $this;
    }

    /**
     * Remove role
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Role $role
     */
    public function removeRole(\fibe\Bundle\WWWConfBundle\Entity\Role $role)
    {
        $this->role->removeElement($role);
    }

    /**
     * Get role
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set parent
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $parent
     * @return ConfEvent
     */
    public function setParent(\fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $children
     * @return ConfEvent
     */
    public function addChildren(\fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $children
     */
    public function removeChildren(\fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add xProperties
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\XProperty $xProperties
     * @return ConfEvent
     */
    public function addXPropertie(\fibe\Bundle\WWWConfBundle\Entity\XProperty $xProperties)
    {
        $this->xProperties[] = $xProperties;
    
        return $this;
    }

    /**
     * Remove xProperties
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\XProperty $xProperties
     */
    public function removeXPropertie(\fibe\Bundle\WWWConfBundle\Entity\XProperty $xProperties)
    {
        $this->xProperties->removeElement($xProperties);
    }

    /**
     * Get xProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getXProperties()
    {
        return $this->xProperties;
    }

    /**
     * Set status
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Status $status
     * @return ConfEvent
     */
    public function setStatus(\fibe\Bundle\WWWConfBundle\Entity\Status $status = null)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\Status 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set location
     *
     * @param \IDCI\Bundle\SimpleScheduleBundle\Entity\Location $location
     * @return ConfEvent
     */
    public function setLocation(\IDCI\Bundle\SimpleScheduleBundle\Entity\Location $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \IDCI\Bundle\SimpleScheduleBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add categories
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Category $categories
     * @return ConfEvent
     */
    public function addCategorie(\fibe\Bundle\WWWConfBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Category $categories
     */
    public function removeCategorie(\fibe\Bundle\WWWConfBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set includedRule
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Recur $includedRule
     * @return ConfEvent
     */
    public function setIncludedRule(\fibe\Bundle\WWWConfBundle\Entity\Recur $includedRule = null)
    {
        $this->includedRule = $includedRule;
    
        return $this;
    }

    /**
     * Get includedRule
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\Recur 
     */
    public function getIncludedRule()
    {
        return $this->includedRule;
    }

    /**
     * Set excludedRule
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Recur $excludedRule
     * @return ConfEvent
     */
    public function setExcludedRule(\fibe\Bundle\WWWConfBundle\Entity\Recur $excludedRule = null)
    {
        $this->excludedRule = $excludedRule;
    
        return $this;
    }

    /**
     * Get excludedRule
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\Recur 
     */
    public function getExcludedRule()
    {
        return $this->excludedRule;
    }
}