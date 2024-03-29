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
     * @ORM\ManyToMany(targetEntity="Theme", inversedBy="confEvents", cascade={"persist"})
     * @ORM\JoinTable(name="confEvent_themes",
     *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="theme_id", referencedColumnName="id", onDelete="Cascade")})
     */
    private $themes;

    /**
     * role
     * Persons related to an event 
     *  
     * @ORM\OneToMany(targetEntity="Role", mappedBy="event")
     */
    private $role;


    /**
     *  
     * Is an all day event ?
     *   
     * @ORM\Column(name="is_allday", type="boolean")
     * 
     */
    private $isAllDay ;

    /**
     * computeIsAllDay
     *
     * @ORM\PrePersist() 
     */
    public function computeIsAllDay()
    {
         

         $start = $this->getStartAt();
         $end = $this->getEndAt(); 
         $this->setIsAllDay($start->format('d')!=$end->format('d')); 

    }


 

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
     * Set isAllDay
     * 
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
     * @return boolean
     */
    public function getIsAllDay()
    {
        return $this->isAllDay;
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
     * Add themes
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Theme $themes
     * @return ConfEvent
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