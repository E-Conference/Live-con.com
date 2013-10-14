<?php 
 
namespace fibe\SecurityBundle\Entity;
 
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

 
/**
 * @ORM\Entity
 * @ORM\Table(name="manager")
 */
class User extends BaseUser
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id; 
    
    /**
     * @ORM\ManyToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confManagers", cascade={"persist"})
     * @ORM\JoinTable(name="manager_conference",
     *     joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")})
     */
    protected $conferences;

    /**
     *  
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf")
     *  @ORM\JoinColumn(name="currentConf", referencedColumnName="id")
     */
    protected $currentConf;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
	    parent::__construct();
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
     * Add conferences
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences
     * @return User
     */
    public function addConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences)
    {
        $this->conferences[] = $conferences;
    
        return $this;
    }

    /**
     * Remove conferences
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences
     */
    public function removeConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences)
    {
        $this->conferences->removeElement($conferences);
    }

    /**
     * Get conferences
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConferences()
    {
        return $this->conferences;
    }

    /**
     * Set currentConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf
     * @return User
     */
    public function setCurrentConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf = null)
    {
        $this->currentConf = $currentConf;
    
        return $this;
    }

    /**
     * Get currentConf
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf 
     */
    public function getCurrentConf()
    {
        return $this->currentConf;
    }
}