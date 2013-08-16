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
     * @OneToOne(targetEntity="paper")
     * @JoinColumn(name="id_paper", referencedColumnName="id")
     *
     */
    protected $id_paper;

    /**
     *  
     * @OneToOne(targetEntity="keyword")
     * @JoinColumn(name="id_keyword, referencedColumnName="id")
     *
     */
    protected $id_keyword;

}
