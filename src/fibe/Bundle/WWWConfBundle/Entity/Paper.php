<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\wwwConf;

/**
 * This entity define a paper of a conference
 *
 *
 *   @ORM\Table(name="paper")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PaperRepository")
 *
 */

class Paper
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * type
     *
     *
     * @ORM\Column(type="string", name="type")
     */
    protected $type;

     /**
     * label
     *
     *
     * @ORM\Column(type="string", name="label")
     */
    protected $label;
	
	/**
     *  Conference associated to this paper
     * @OneToOne(targetEntity="wwwConf")
     * @JoinColumn(name="id_wwwconf", referencedColumnName="id")
     *
     */
    protected $conference;

    /**
     * title
     *
     *
     * @ORM\Column(type="string", name="title")
     */
    protected $title;


    /**
     * abstract
     *
     *
     * @ORM\Column(type="text", name="abstract")
     */
    protected $abstract;

    /**
     * month
     *
     *
     * @ORM\Column(type="string", name="month")
     */
    protected $month;

     /**
     * year
     *
     *
     * @ORM\Column(type="string", name="year")
     */
    protected $year;

     /**
     * url_pdf
     *
     *
     * @ORM\Column(type="string", name="url_pdf")
     */
    protected $url_pdf;
}
