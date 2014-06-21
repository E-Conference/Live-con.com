<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;

use fibe\Bundle\WWWConfBundle\Util\StringTools;

/**
 * This entity is based on the specification FOAF.
 *
 * This class define a Person.
 * @ORM\Table(name="person")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PersonRepository")
 * @ExclusionPolicy("all") 
 *
 */
class Person
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
   * A name for some thing. Name of the person
   * / ! \  auto built with the concatenation of first and last name
   * @ORM\Column(type="string", name="name")
   * @Expose
   */
  protected $name;

  /**
   * familyName
   * @Assert\NotBlank(message ="Please give a family name")
   * @ORM\Column(type="string", nullable=true,  name="familyName")
   */
  protected $familyName;

  /**
   * firstName
   * @Assert\NotBlank(message ="Please give a first name")
   * @ORM\Column(type="string", nullable=true,  name="firstName")
   */
  protected $firstName;

  /**
   * based_near
   * @ORM\Column(type="string", nullable=true,  name="based_near")
   * @Expose
   */
  protected $based_near;

  /**
   * description
   *
   * @ORM\Column(type="string", length=1024, nullable=true, name="description")
   * @Expose
   */
  protected $description;

  /**
   * age
   *
   *
   * @ORM\Column(type="integer", nullable=true,  name="age")
   * @Expose
   */
  protected $age;

  /**
   * Paper
   * Paper made by this person
   *
   * @ORM\ManyToMany(targetEntity="Paper",  mappedBy="authors", cascade={"remove","persist","merge"})
   */
  private $papers;

  /**
   * Organizations
   *
   *
   * @ORM\ManyToMany(targetEntity="Organization", inversedBy="members", cascade={"remove","persist","merge"})
   * @ORM\JoinTable(name="member",
   *     joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="Cascade")})
   *     inverseJoinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="Cascade")},
   * @Expose
   */
  private $organizations;

  /**
   * img
   *
   * @ORM\Column(type="string", nullable=true,  name="img")
   */
  protected $img;

  /**
   * openId
   *
   *
   * @ORM\Column(type="string", nullable=true,  name="openId")
   */
  protected $openId;

  /**
   *
   * @ORM\OneToMany(targetEntity="Role",  mappedBy="person",cascade={"persist","remove"})
   * @ORM\JoinColumn(onDelete="CASCADE")
   *
   */
  private $roles;

  /**
   *  Topics associated to this conference
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="persons", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
  protected $conference;

  /**
   * email
   *
   *
   * @ORM\Column(type="string", nullable=true,  name="email")
   */
  protected $email;

  /**
   * emailSha1
   *
   *
   * @ORM\Column(type="string", nullable=true,  name="emailSha1")
   */
  protected $emailSha1;

  /**
   * page
   * person's homepage url
   *
   * @ORM\Column(type="string", length=255, nullable=true, name="page")
   */
  protected $page;

  /**
   *
   * @ORM\OneToMany(targetEntity="SocialServiceAccount",  mappedBy="owner", cascade={"persist", "remove"})
   *
   */
  protected $accounts;

  /**
   * @ORM\Column(type="string", length=256, nullable=true)
   */
  protected $slug;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
    $this->organization = new \Doctrine\Common\Collections\ArrayCollection();
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * __toString method
   *
   * @return mixed
   */
  public function __toString()
  {
    return $this->name;

  }

  /**
   * onCreation
   *
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function computeName()
  {
    $this->setName($this->firstName . " " . $this->familyName);
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
   * @return Person
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
   * Set familyName
   *
   * @param string $familyName
   *
   * @return Person
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;

    return $this;
  }

  /**
   * Get familyName
   *
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }

  /**
   * Set firstName
   *
   * @param string $firstName
   *
   * @return Person
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;

    return $this;
  }

  /**
   * Get firstName
   *
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }

  /**
   * Set based_near
   *
   * @param string $basedNear
   *
   * @return Person
   */
  public function setBasedNear($basedNear)
  {
    $this->based_near = $basedNear;

    return $this;
  }

  /**
   * Get based_near
   *
   * @return string
   */
  public function getBasedNear()
  {
    return $this->based_near;
  }

  /**
   * Set description
   *
   * @param string $description
   *
   * @return Person
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
   * Set age
   *
   * @param integer $age
   *
   * @return Person
   */
  public function setAge($age)
  {
    $this->age = $age;

    return $this;
  }

  /**
   * Get age
   *
   * @return integer
   */
  public function getAge()
  {
    return $this->age;
  }

  /**
   * Set img
   *
   * @param string $img
   *
   * @return Person
   */
  public function setImg($img)
  {
    $this->img = $img;

    return $this;
  }

  /**
   * Get img
   *
   * @return string
   */
  public function getImg()
  {
    return $this->img;
  }

  /**
   * Set openId
   *
   * @param string $openId
   *
   * @return Person
   */
  public function setOpenId($openId)
  {
    $this->openId = $openId;

    return $this;
  }

  /**
   * Get openId
   *
   * @return string
   */
  public function getOpenId()
  {
    return $this->openId;
  }

  /**
   * Set email
   *
   * @param string $email
   *
   * @return Person
   */
  public function setEmail($email)
  {
    $this->email = $email;
    $this->emailSha1 = sha1("mailto:" . $email);

    return $this;
  }

  /**
   * Get email
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Set emailSha1
   *
   * @param string $emailSha1
   *
   * @return Person
   */
  public function setEmailSha1($emailSha1)
  {
    $this->emailSha1 = $emailSha1;

    return $this;
  }

  /**
   * Get emailSha1
   *
   * @return string
   */
  public function getEmailSha1()
  {
    return $this->emailSha1;
  }

  /**
   * Set page
   *
   * @param string $page
   *
   * @return Person
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
   * Add papers
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
   *
   * @return Person
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
   * Add organizations
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
   *
   * @return Person
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
   * Add roles
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Role $roles
   *
   * @return Person
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
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
   * @return Person
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

  /**
   * Add accounts
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts
   *
   * @return Person
   */
  public function addAccount(\fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts)
  {
    $this->accounts[] = $accounts;

    return $this;
  }

  /**
   * Remove accounts
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts
   */
  public function removeAccount(\fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts)
  {
    $this->accounts->removeElement($accounts);
  }

  /**
   * Get accounts
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAccounts()
  {
    return $this->accounts;
  }
}
