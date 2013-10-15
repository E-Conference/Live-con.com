<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\SimpleScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IDCI\Bundle\SimpleScheduleBundle\Util\StringTools;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="IDCI\Bundle\SimpleScheduleBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Category
{ 

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $color;
  
    /**
     * @ORM\ManyToMany(targetEntity="CalendarEntity", mappedBy="categories", cascade={"persist"})
     */
    private $calendarEntities;

    /**
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Constructor
     */
    public function __construct()
    { 
        $this->calendarEntities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Slugify
     */
    public function slugify()
    {
        $this->setSlug(StringTools::slugify($this->getName()));
    } 
    /**
     * onUpdate
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function onUpdate()
    {
        $now = new \DateTime('now');

        $this->slugify(); 
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
     * @return Category
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
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
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
     * Set color
     *
     * @param string $color
     * @return Category
     */
    public function setColor($color)
    {
        $this->color = $color;
    
        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    { 
        return $this->color;
    }  
    /**
     * Add calendarEntities
     *
     * @param \IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntities
     * @return Category
     */
    public function addCalendarEntitie(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntities)
    {
        $this->calendarEntities[] = $calendarEntities;
    
        return $this;
    }

    /**
     * Remove calendarEntities
     *
     * @param \IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntities
     */
    public function removeCalendarEntitie(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntities)
    {
        $this->calendarEntities->removeElement($calendarEntities);
    }

    /**
     * Get calendarEntities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCalendarEntities()
    {
        return $this->calendarEntities;
    }
}
