<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Entity\Keyword;


/**
 * This entity define relation is subject of a paper 
 *
 *
 *  @ORM\Table(name="subject")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\SubjectRepository")
 *
 */

class Subject
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    
    /**
     *  
     *@ORM\OneToOne(targetEntity="paper")
     * @ORM\JoinColumn(name="id_paper", referencedColumnName="id")
     *
     */
    protected $id_paper;

    /**
     *  
     *@ORM\OneToOne(targetEntity="keyword")
     * @ORM\JoinColumn(name="id_keyword", referencedColumnName="id")
     *
     */
    protected $id_keyword;


    

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
     * Set id_paper
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\paper $idPaper
     * @return Subject
     */
    public function setIdPaper(\fibe\Bundle\WWWConfBundle\Entity\paper $idPaper = null)
    {
        $this->id_paper = $idPaper;
    
        return $this;
    }

    /**
     * Get id_paper
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\paper 
     */
    public function getIdPaper()
    {
        return $this->id_paper;
    }

    /**
     * Set id_keyword
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\keyword $idKeyword
     * @return Subject
     */
    public function setIdKeyword(\fibe\Bundle\WWWConfBundle\Entity\keyword $idKeyword = null)
    {
        $this->id_keyword = $idKeyword;
    
        return $this;
    }

    /**
     * Get id_keyword
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\keyword 
     */
    public function getIdKeyword()
    {
        return $this->id_keyword;
    }
}