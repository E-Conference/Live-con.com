<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;

/**
 * This entity is based on the specification FOAF.
 *
 * This class define a Person.
 *   @ORM\Table(name="person")
 *   @ORM\HasLifecycleCallbacks
 *   @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PersonRepository")
 * 	
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *  
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * 
     */
    private $role;

    /**
     *  Homepage
     *
     * @ORM\Column(type="string", name="homepage")
     * 
     */
    private $homepage;

    /**
     * paper
     * Paper make by this person
     *  
     * @ORM\OneToMany(targetEntity="Author", mappedBy="person")
     */
    private $paper;

    /**
     * Organizations
     * Organizations where the person is member
     *
     * @ORM\ManyToMany(targetEntity="Organization", mappedBy="members", cascade={"persist"})
     */
    private $organizations;
    

    /**
     * email
     *
     *
     * @ORM\Column(type="string", name="email")
     */
    protected $email;

    /**
    *   Hashtag
    *   @ORM\Column(type="string", name="hashtag")
    */
    protected $hashtag;

    /**
     * agent
     *
     * An agent (eg. person, group, software or physical artifact)
     *
     * @ORM\Column(type="string", name="agent")
     */
    protected $agent;

    /**
     * name
     * A name for some thing. Name of the person 
     * @ORM\Column(type="string", name="name")
     */
    protected $name;

    /**
     * title
     *
	* Title (Mr, Mrs, Ms, Dr. etc) 
     *
     * @ORM\Column(type="string", length=255, nullable=true,name="title")
     */
    protected $title;

    /**
     * img
     *
     *  image - An image that can be used to represent some thing (ie. those depictions which are particularly representative of something, eg. one's photo on a homepage). 
     * Status:	testing
     * Domain:	having this property implies being a Person
     * Range : every value of this property is a Image
     *
     * @ORM\Column(type="string", nullable=true, name="img")
     */
    protected $img;

    /**
     * depiction
     *
     * depiction - A depiction of some thing. 
	 * Status:	testing
	 * Domain:	having this property implies being a Thing
	 * Range:	every value of this property is a Image
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="depiction")
     */
     protected $depiction;

    /**
     * familyName
     *. familyName - The family name of some person. 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="familyName")
     */
     protected $familyName;

    /**
     * givenName
     *
     * Given name - The given name of some person. 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="givenName")
     */
     protected $givenName;

    /**
     * based_near
     *
     * Ibased near - A location that something is based near, for some broadly human notion of near.
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="based_near")
     */
     protected $based_near;

    /**
     * knows
     *
     * knows - A person known by this person (indicating some level of reciprocated interaction between the parties). 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="knows")
     */
     protected $knows;

    /**
     * age
     *
     * age - The age in years of some agent. 
     *
     * @ORM\Column(type="string", length=32, name="age")
     */
    protected $age ;

    /**
     * made
     * 
     * made - Something that was made by this agent. 
     *
     * @ORM\Column(type="string", length=32, name="made")
     */
    protected $made;
	
	 /**
     * primary_topic
     * 
     * primary topic - The primary topic of some page or document. 
     *
     * @ORM\Column(type="string", length=32, name="primary_topic")
     */
    protected $primary_topic;

    /**
     * project
     *
     * Project - A project (a collective endeavour of some kind). 
     *
     *  @ORM\Column(type="string", length=32, name="project")
     */
     protected $project;

    /**
     * organization
     * Organization - An organization
	* @ORM\Column(type="string", length=32, name="organization")
     */
    protected $organization;

    /**
     * group
     *
     * Group - A class of Agents. 
	* @ORM\Column(type="string", length=32, name="_group")
     */
    protected $_group;

    /**
     * member
     *
     * member - Indicates a member of a Group 
     *  @ORM\Column(type="string", length=32, name="member")
     */
     protected $member;

    /**
     * document
     *
     * Document - A document.
	* @ORM\Column(type="string", length=32, name="document")
     */
     protected $document;

    /**
     * image
     *
     * Image - An image. 
	* Status:	testing
	* Properties include:	thumbnail depicts
	* Used with:	thumbnail depiction img
	* Subclass Of	Document
	* The class Image is a sub-class of Document corresponding to those documents which are images.
	* Digital images (such as JPEG, PNG, GIF bitmaps, SVG diagrams etc.) are examples of Image.
	*@ORM\Column(type="string", length=32, name="image")
     */

    protected $image;
	 
	

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paper = new \Doctrine\Common\Collections\ArrayCollection();
        $this->organizations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
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

<<<<<<< HEAD
   
=======



    /**
     * onCreation
     *
     * @ORM\PrePersist()
     */
    public function onCreation()
    {
        $now = new \DateTime('now');

        $this->setCreatedAt($now); 
    }
    

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Person
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

>>>>>>> 8be7114a079eaa516b464549ff8d8b7bce95a68d
    /**
     * Set agent
     *
     * @param string $agent
     * @return Person
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    
        return $this;
    }

    /**
     * Get agent
     *
     * @return string 
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set name
     *
     * @param string $name
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
     * Set title
     *
     * @param string $title
     * @return Person
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set img
     *
     * @param string $img
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
     * Set depiction
     *
     * @param string $depiction
     * @return Person
     */
    public function setDepiction($depiction)
    {
        $this->depiction = $depiction;
    
        return $this;
    }

    /**
     * Get depiction
     *
     * @return string 
     */
    public function getDepiction()
    {
        return $this->depiction;
    }

    /**
     * Set familyName
     *
     * @param string $familyName
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
     * Set givenName
     *
     * @param string $givenName
     * @return Person
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    
        return $this;
    }

    /**
     * Get givenName
     *
     * @return string 
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Set based_near
     *
     * @param string $basedNear
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
     * Set knows
     *
     * @param string $knows
     * @return Person
     */
    public function setKnows($knows)
    {
        $this->knows = $knows;
    
        return $this;
    }

    /**
     * Get knows
     *
     * @return string 
     */
    public function getKnows()
    {
        return $this->knows;
    }

    /**
     * Set age
     *
     * @param string $age
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
     * @return string 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set made
     *
     * @param string $made
     * @return Person
     */
    public function setMade($made)
    {
        $this->made = $made;
    
        return $this;
    }

    /**
     * Get made
     *
     * @return string 
     */
    public function getMade()
    {
        return $this->made;
    }

    /**
     * Set primary_topic
     *
     * @param string $primaryTopic
     * @return Person
     */
    public function setPrimaryTopic($primaryTopic)
    {
        $this->primary_topic = $primaryTopic;
    
        return $this;
    }

    /**
     * Get primary_topic
     *
     * @return string 
     */
    public function getPrimaryTopic()
    {
        return $this->primary_topic;
    }

    /**
     * Set project
     *
     * @param string $project
     * @return Person
     */
    public function setProject($project)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return string 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set organization
     *
     * @param string $organization
     * @return Person
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    
        return $this;
    }

    /**
     * Get organization
     *
     * @return string 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set _group
     *
     * @param string $group
     * @return Person
     */
    public function set_group($group)
    {
        $this->_group = $group;
    
        return $this;
    }

    /**
     * Get _group
     *
     * @return string 
     */
    public function get_group()
    {
        return $this->_group;
    }

    /**
     * Set member
     *
     * @param string $member
     * @return Person
     */
    public function setMember($member)
    {
        $this->member = $member;
    
        return $this;
    }

    /**
     * Get member
     *
     * @return string 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set document
     *
     * @param string $document
     * @return Person
     */
    public function setDocument($document)
    {
        $this->document = $document;
    
        return $this;
    }

    /**
     * Get document
     *
     * @return string 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Person
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add role
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Role $role
     * @return Person
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
     * Add paper
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Author $paper
     * @return Person
     */
    public function addPaper(\fibe\Bundle\WWWConfBundle\Entity\Author $paper)
    {
        $this->paper[] = $paper;
    
        return $this;
    }

    /**
     * Remove paper
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Author $paper
     */
    public function removePaper(\fibe\Bundle\WWWConfBundle\Entity\Author $paper)
    {
        $this->paper->removeElement($paper);
    }

    /**
     * Get paper
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaper()
    {
        return $this->paper;
    }

    /**
     * Add organizations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set hashtag
     *
     * @param string $hashtag
     * @return Person
     */
    public function setHashtag($hashtag)
    {
        $this->hashtag = $hashtag;
    
        return $this;
    }

    /**
     * Get hashtag
     *
     * @return string 
     */
    public function getHashtag()
    {
        return $this->hashtag;
    }

    /**
     * Set _group
     *
     * @param string $group
     * @return Person
     */
    public function setGroup($group)
    {
        $this->_group = $group;
    
        return $this;
    }

    /**
     * Get _group
     *
     * @return string 
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Person
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }
}