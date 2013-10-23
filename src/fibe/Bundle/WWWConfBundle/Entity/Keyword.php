<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a keyword
 *
 *  @ORM\Table(name="keyword")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\KeywordRepository")
 *
 */

class Keyword
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * name
     *
     * @ORM\Column(type="string", name="name")
     */
    protected $name;

    /**
     * Papers related to thise keyword
     *
     * @ORM\ManyToMany(targetEntity="Paper", mappedBy="subjects", cascade={"persist"})
     */
    private $papers;

    /**
     *  Themes associated to this conference
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="keywords", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     *
     */
    protected $conference;
        


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
    }

     public function __toString() 
    {
        return $this->name;

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
     * Add papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     * @return Keyword
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
     * Set name
     *
     * @param string $name
     * @return Keyword
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
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return Keyword
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