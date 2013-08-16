<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\Paper;


/**
 * This entity define relation between a paper and a person
 *
 * 
 *  @ORM\Table(name="author")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\AuthorRepository")
 *
 */

class Author
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    
    /**
     *  
     * @ORM\OneToOne(targetEntity="person")
     * @ORM\JoinColumn(name="id_person", referencedColumnName="id")
     *
     */
    protected $id_person;

    /**
     *  
     *@ORM\OneToOne(targetEntity="paper")
     * @ORM\JoinColumn(name="id_paper", referencedColumnName="id")
     *
     */
    protected $id_paper;

}
