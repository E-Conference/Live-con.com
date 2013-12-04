<?php 
 
namespace fibe\SecurityBundle\Entity;
 
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

 
/**
 * @ORM\Entity
 * @ORM\Table(name="authorization")
 */
class Authorization
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id; 
    
   /**
     *  
     * @ORM\ManyToOne(targetEntity="User", inversedBy="authorizations")
     *
     */
    protected $user;

   /**
     *  
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="authorizations")
     *
     */
    protected $conference;

    /**
    * @ORM\Column(type="boolean",options={"default" = 0})
    * Flag who gives read/write authorization on DataConf configurations
    */
    protected $flagApp;

    /**
    * @ORM\Column(type="boolean",options={"default" = 0})
    * Flag who gives read/write authorization on events managing
    */
    protected $flagSched;

    /**
    * @ORM\Column(type="boolean",options={"default" = 0})
    * Flag who gives read/write authorization on conference datas(papers, persons, roles, topics ...)
    */
    protected $flagconfDatas;
    
    
   

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
     * Set flagApp
     *
     * @param boolean $flagApp
     * @return Authorization
     */
    public function setFlagApp($flagApp)
    {
        $this->flagApp = $flagApp;
    
        return $this;
    }

    /**
     * Get flagApp
     *
     * @return boolean 
     */
    public function getFlagApp()
    {
        return $this->flagApp;
    }

    /**
     * Set flagSched
     *
     * @param boolean $flagSched
     * @return Authorization
     */
    public function setFlagSched($flagSched)
    {
        $this->flagSched = $flagSched;
    
        return $this;
    }

    /**
     * Get flagSched
     *
     * @return boolean 
     */
    public function getFlagSched()
    {
        return $this->flagSched;
    }

    /**
     * Set flagconfDatas
     *
     * @param boolean $flagconfDatas
     * @return Authorization
     */
    public function setFlagconfDatas($flagconfDatas)
    {
        $this->flagconfDatas = $flagconfDatas;
    
        return $this;
    }

    /**
     * Get flagconfDatas
     *
     * @return boolean 
     */
    public function getFlagconfDatas()
    {
        return $this->flagconfDatas;
    }

    /**
     * Set user
     *
     * @param \fibe\SecurityBundle\Entity\User $user
     * @return Authorization
     */
    public function setUser(\fibe\SecurityBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \fibe\SecurityBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return Authorization
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