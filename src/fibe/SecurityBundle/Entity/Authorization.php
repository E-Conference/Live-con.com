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
    protected $flagAppWR;

    /**
    * @ORM\Column(type="boolean",options={"default" = 0})
    * Flag who gives read/write authorization on events managing
    */
    protected $flagSchedWR;

    /**
    * @ORM\Column(type="boolean",options={"default" = 0})
    * Flag who gives read/write authorization on conference datas(papers, persons, roles, topics ...)
    */
    protected $flagconfDatasWR;
    
    
   

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
     * Set flagAppWR
     *
     * @param boolean $flagAppWR
     * @return Authorization
     */
    public function setFlagAppWR($flagAppWR)
    {
        $this->flagAppWR = $flagAppWR;
    
        return $this;
    }

    /**
     * Get flagAppWR
     *
     * @return boolean 
     */
    public function getFlagAppWR()
    {
        return $this->flagAppWR;
    }

    /**
     * Set flagSchedWR
     *
     * @param boolean $flagSchedWR
     * @return Authorization
     */
    public function setFlagSchedWR($flagSchedWR)
    {
        $this->flagSchedWR = $flagSchedWR;
    
        return $this;
    }

    /**
     * Get flagSchedWR
     *
     * @return boolean 
     */
    public function getFlagSchedWR()
    {
        return $this->flagSchedWR;
    }

    /**
     * Set flagconfDatasWR
     *
     * @param boolean $flagconfDatasWR
     * @return Authorization
     */
    public function setFlagconfDatasWR($flagconfDatasWR)
    {
        $this->flagconfDatasWR = $flagconfDatasWR;
    
        return $this;
    }

    /**
     * Get flagconfDatasWR
     *
     * @return boolean 
     */
    public function getFlagconfDatasWR()
    {
        return $this->flagconfDatasWR;
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