<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Util\StringTools;

/**
 * This entity define a role for a person in an event
 *
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\OrganizationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all") 
 *
 */
class Organization
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Expose
   */
  protected $id;

  /**
   * name
   *
   *
   * @ORM\Column(type="string", name="name")
   * @Expose
   */
  protected $name;

  /**
   * page
   * @ORM\Column(type="string", name="page",nullable=true)
   * @Expose
   */
  protected $page;

  /**
   * country
   *
   *
   * @ORM\Column(type="string", name="country",nullable=true)
   */
  protected $country;

  /**
   * @ORM\ManyToMany(targetEntity="Person",  mappedBy="organizations", cascade={"persist","merge","remove"})
   * @Expose
   */
  protected $members;

  /**
   *  Topics associated to this conference
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="organizations", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
  protected $conference;

  /**
   * @ORM\Column(type="string", length=256, nullable=true)
   */
  protected $slug;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->members = new \Doctrine\Common\Collections\ArrayCollection();
  }


  public function __toString()
  {
    return $this->name;
  }

  /**
   * Slugify
   * @ORM\PrePersist()
   */
  public function slugify()
  {
    $this->setSlug(StringTools::slugify($this->getId() . $this->getName()));
  }

  /**
   * onUpdate
   *
   * @ORM\PostPersist()
   * @ORM\PreUpdate()
   */
  public function onUpdate()
  {
    $this->slugify();
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
   *
   * @return Organization
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
   * Set page
   *
   * @param string $page
   *
   * @return Organization
   */
  public function setPage($page)
  {
    $this->page = $page;

    return $this;
  }

  /**
   * Get page
   *
   * @return string
   */
  public function getPage()
  {
    return $this->page;
  }

  /**
   * Set country
   *
   * @param string $country
   *
   * @return Organization
   */
  public function setCountry($country)
  {
    $this->country = $country;

    return $this;
  }

  /**
   * Get country
   *
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }

  /**
   * Add members
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Person $members
   *
   * @return Organization
   */
  public function addMember(\fibe\Bundle\WWWConfBundle\Entity\Person $members)
  {
    $this->members[] = $members;

    return $this;
  }

  /**
   * Remove members
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Person $members
   */
  public function removeMember(\fibe\Bundle\WWWConfBundle\Entity\Person $members)
  {
    $this->members->removeElement($members);
  }

  /**
   * Get members
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMembers()
  {
    return $this->members;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
   * @return Organization
   */
  public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference = null)
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
}
