<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Event;
use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\Paper;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

use fibe\Bundle\WWWConfBundle\Util\StringTools;


/**
 * ConfEvent
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\ConfEventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ConfEvent extends Event
{
  /**
   * status
   *
   * @ORM\ManyToOne(targetEntity="ConfEvent", inversedBy="children", cascade={"persist","detach"})
   * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
   * @Expose
   */
  protected $parent;

  /**
   * @ORM\OneToMany(targetEntity="ConfEvent", mappedBy="parent", cascade={"persist"})
   * @Expose
   */
  protected $children;

  /**
   * slidePresentation
   * Url to slides presentation
   *
   * @ORM\Column(type="string", nullable=true,  name="slidePresentation")
   * 
   */
  protected $slidePresentation;

  /**
   * conference
   *
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="events", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   * @Expose
   */
  private $conference;

  /**
   * @ORM\ManyToMany(targetEntity="Paper", inversedBy="events", cascade={"persist"})
   * @ORM\JoinTable(name="confEvent_paper",
   *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id")},
   *     inverseJoinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id")})
   * @Expose
   */
  private $papers;

  /**
   * @ORM\ManyToMany(targetEntity="Topic", inversedBy="events", cascade={"persist"})
   * @ORM\JoinTable(name="confEvent_topic",
   *     joinColumns={@ORM\JoinColumn(name="confEvent_id", referencedColumnName="id")},
   *     inverseJoinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")})
   * @Expose
   */
  private $topics;

  /**
   * roles
   * Persons related to an event
   *
   * @ORM\OneToMany(targetEntity="Role", mappedBy="event",cascade={"persist","remove"})
   * @ORM\JoinColumn( onDelete="CASCADE")
   * @Expose
   */
  private $roles;

  /**
   *
   * Is an all day event
   * Used for ui representation in the calendar view
   *
   * @ORM\Column(name="is_allday", type="boolean")
   * @Expose
   */
  private $isAllDay;

  /**
   * @ORM\Column(type="string", length=128, nullable=true)
   */
  protected $acronym;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $attach;

  /**
   *
   * Is it a main conf event ?
   *
   * @ORM\Column(name="is_mainConfEvent", type="boolean")
   */
  private $isMainConfEvent = false;

  /**
   * @ORM\Column(type="string", length=128, nullable=true)
   */
  protected $slug;

  /**
   * @ORM\Column(name="is_instant", type="boolean")
   */
  protected $isInstant;

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
    $this->topics = new \Doctrine\Common\Collections\ArrayCollection();
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    $this->xProperties = new \Doctrine\Common\Collections\ArrayCollection();
    $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Slugify
   * @ORM\PrePersist()
   */
  public function slugify()
  {
    $this->setSlug(StringTools::slugify($this->getId() . $this->getSummary()));
  }

  /**
   * onUpdate
   *
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function onUpdate()
  {
    $this->slugify();
    $this->setIsInstant($this->getEndAt()->format('U') == $this->getStartAt()->format('U'));

    //events that aren't leaf in the hierarchy can't have a location
    if ($this->hasChildren() && $this->getLocation() != null)
    {
      $this->setLocation(null);
    }

    //ensure main conf has correct properties
    if ($this->isMainConfEvent)
    {
      $this->fitChildrenDate(true);
      if ($this->getEndAt()->getTimestamp() <= $this->getStartAt()->getTimestamp())
      {
        $endAt = clone $this->getStartAt();
        $this->setEndAt($endAt->add(new \DateInterval('P1D')));
      }
      $this->setIsInstant(false);
    }
  }


  /**
   * Ensure main conf event fits its children dates
   *
   * @param bool $allDay
   *
   * @return bool
   */
  public function fitChildrenDate($allDay = false)
  {
    $earliestStart = new \DateTime('6000-10-10');
    $latestEnd = new \DateTime('1000-10-10');
    foreach ($this->getChildren() as $child)
    {
      if ($child->getIsInstant())
      {
        continue;
      }
      if ($child->getStartAt() < $earliestStart)
      {
        $earliestStart = $child->getStartAt();
      }
      if ($child->getEndAt() > $latestEnd)
      {
        $latestEnd = $child->getEndAt();
      }
    }
    if ($earliestStart == new \DateTime('6000-10-10') || $latestEnd == new \DateTime('1000-10-10'))
    {
      return;
    }
    if ($earliestStart == $latestEnd)
    {
      $latestEnd->add(new \DateInterval('P1D'));
    }
    // get startof and endof day
    // if($allDay==true){

    //     $sTS = $earliestStart->getTimestamp();
    //     $eTS = $latestEnd->getTimestamp();

    //     //adjust timezone to correctly calculate with floor()
    //     $tzOffset = \DateTime::createFromFormat('U', $sTS);
    //     $tzOffset->setTimeZone(new \DateTimeZone(date_default_timezone_get()));

    //     $sTS = $sTS + $tzOffset->getOffset();
    //     $sTS = (floor($sTS/86400))*86400;

    //     $eTS = $eTS - $tzOffset->getOffset();
    //     $eTS = (floor(($eTS)/86400))*86400;

    //     //adjust timezone back
    //     $earliestStart = $earliestStart->setTimestamp($sTS - $tzOffset->getOffset());
    //     $latestEnd = $latestEnd->setTimestamp($eTS + $tzOffset->getOffset());
    // }
    if ($earliestStart->getTimestamp() != $this->getStartAt()->getTimestamp() || $latestEnd->getTimestamp() != $this->getEndAt()->getTimestamp())
    {
      $this->setStartAt($earliestStart);
      $this->setEndAt($latestEnd);

      return true;
    }
  }

  /**
   * Set slug
   *
   * @param string $slug
   *
   * @return ConfEvent
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
    $this->slugify();

    return $this->slug;
  }


  /** computeIsAllDay
   *
   * this is only computed on creation for the importer
   * @ORM\PrePersist()
   */
  public function computeIsAllDay()
  {
    if ($this->isMainConfEvent)
    {
      $this->setIsAllDay(true);
    }
  }

  /**
   * Set slidePresentation
   *
   * @param string $slidePresentation
   *
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
   *
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
   * Set Attach
   *
   * @param string $attach
   *
   * @return ConfEvent
   */
  public function setAttach($attach)
  {
    $this->attach = $attach;

    return $this;
  }

  /**
   * Get Attach
   *
   * @return string
   */
  public function getAttach()
  {
    return $this->attach;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
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
   *
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
   *
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
   *
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
   *
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

  /**
   * Set isInstant
   *
   * @param string $isInstant
   *
   * @return ConfEvent
   */
  public function setIsInstant($isInstant)
  {
    $this->isInstant = $isInstant;

    return $this;
  }

  /**
   * Get isInstant
   *
   * @return string
   */
  public function getIsInstant()
  {
    return $this->isInstant;
  }

  /**
   * Set acronym
   *
   * @param string $acronym
   *
   * @return ConfEvent
   */
  public function setAcronym($acronym)
  {
    $this->acronym = $acronym;

    return $this;
  }

  /**
   * Get acronym
   *
   * @return string
   */
  public function getAcronym()
  {
    return $this->acronym;
  }

  /**
   * Set parent
   *
   * @param ConfEvent $parent
   *
   * @return ConfEvent
   */
  public function setParent(ConfEvent $parent = null)
  {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Get parent
   *
   * @return ConfEvent
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * Add children
   *
   * @param ConfEvent $children
   *
   * @return ConfEvent
   */
  public function addChildren(ConfEvent $children)
  {
    $this->children[] = $children;

    return $this;
  }

  /**
   * Remove children
   *
   * @param ConfEvent $children
   */
  public function removeChildren(ConfEvent $children)
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
   * Has children
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function hasChildren()
  {
    return count($this->children) != 0;
  }

}
