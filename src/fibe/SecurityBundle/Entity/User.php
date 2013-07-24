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
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confManagers", cascade={"persist"})
     * @ORM\JoinColumn(name="wwwConf_id", referencedColumnName="id")
     */
    protected $wwwConf;
    
    
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
}
