<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use fibe\Bundle\WWWConfBundle\Util\StringTools;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Category
{
    /**
     * Get tree separator
     *
     * @return string
     */
    public static function getTreeSeparator()
    {
        return '-';
    }

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
     * @ORM\Column(type="string", length=128)
     */
    protected $label;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $level;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tree;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $childs;

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
        if($this->getParent()) {
            return sprintf('%s > %s',
                $this->getParent(),
                $this->getLabel()
            );
        }

        return $this->getLabel();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * hasParent
     *
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->getParent();
    }

    /**
     * buildTree
     *
     * @return string
     */
    public function buildTree()
    {
        if(!$this->hasParent()) {
            return null;
        }

        return sprintf('%s%d%s',
            $this->getParent()->getTree(),
            $this->getParent()->getId(),
            self::getTreeSeparator()
        );
    }

    /**
     * updateTree
     *
     * @return boolean
     */
    public function updateTree()
    {
        $tree = $this->getTree();
        $this->setTree($this->buildTree());

        return $tree != $this->getTree();
    }

    /**
     * countLevel
     *
     * @return integer
     */
    public function countLevel()
    {
        if(!$this->hasParent()) {
            return 0;
        }

        return $this->getParent()->getLevel() + 1;
    }

    /**
     * updateLevel
     *
     * @return boolean
     */
    public function updateLevel()
    {
        $level = $this->getLevel();
        $this->setLevel($this->countLevel());

        return $level != $this->getLevel();
    }

    /**
     * updateHierachyFields
     *
     * @return boolean
     */
    public function updateHierachyFields()
    {
        $treeUpdated = $this->updateTree();
        $levelUpdated = $this->updateLevel();

        return $treeUpdated || $levelUpdated;
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
        $this->updateHierachyFields();
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
     * Set label
     *
     * @param string $label
     * @return Category
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
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
        if(null === $this->color && $this->hasParent()) {
            return $this->getParent()->getColor();
        }

        return $this->color;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return Category
     */
    public function setLevel($level)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set tree
     *
     * @param string $tree
     * @return Category
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    
        return $this;
    }

    /**
     * Get tree
     *
     * @return string 
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Set parent
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(\fibe\Bundle\WWWConfBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\Category 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add childs
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Category $childs
     * @return Category
     */
    public function addChild(\fibe\Bundle\WWWConfBundle\Entity\Category $childs)
    {
        $this->childs[] = $childs;
    
        return $this;
    }

    /**
     * Remove childs
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Category $childs
     */
    public function removeChild(\fibe\Bundle\WWWConfBundle\Entity\Category $childs)
    {
        $this->childs->removeElement($childs);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Add calendarEntities
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $calendarEntities
     * @return Category
     */
    public function addCalendarEntitie(\fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $calendarEntities)
    {
        $this->calendarEntities[] = $calendarEntities;
    
        return $this;
    }

    /**
     * Remove calendarEntities
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $calendarEntities
     */
    public function removeCalendarEntitie(\fibe\Bundle\WWWConfBundle\Entity\CalendarEntity $calendarEntities)
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