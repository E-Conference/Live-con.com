<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a keyword
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\KeywordRepository")
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
   * libelle
   *
   *
   * @ORM\Column(type="string", name="libelle")
   */
  protected $libelle;

  /**
   * Papers related to thise keyword
   *
   * @ORM\ManyToMany(targetEntity="Paper", mappedBy="keywords", cascade={"persist"})
   */
  private $papers;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set libelle
   *
   * @param string $libelle
   *
   * @return Keyword
   */
  public function setLibelle($libelle)
  {
    $this->libelle = $libelle;

    return $this;
  }

  /**
   * Get libelle
   *
   * @return string
   */
  public function getLibelle()
  {
    return $this->libelle;
  }

  /**
   * Add papers
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
   *
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
}