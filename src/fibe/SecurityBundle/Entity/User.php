<?php 
 
namespace fibe\SecurityBundle\Entity;
 
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

 
/**
 * @ORM\Entity
 * @ORM\Table(name="wwwconf_manager")
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
    * wwwConf
    *
    * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", mappedBy="confManager", cascade={"persist", "remove"}) 
    */
    
    protected $wwwConf;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
	    parent::__construct(); 
        $this->wwwConf = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add wwwConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf
     * @return User
     */
    public function addWwwConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf)
    {
        $this->wwwConf[] = $wwwConf;
    
        return $this;
    }

    /**
     * Remove wwwConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf
     */
    public function removeWwwConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf)
    {
        $this->wwwConf->removeElement($wwwConf);
    }

    /**
     * Get wwwConf
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWwwConf()
    {
        return $this->wwwConf;
    }
}
