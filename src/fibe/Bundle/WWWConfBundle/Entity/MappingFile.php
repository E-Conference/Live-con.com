<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a mapping file
 *
 *
 * @ORM\Table(name="mappingFile")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\MappingFileRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MappingFile
{

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * mapping
   *
   *
   * @ORM\Column(type="text", name="mapping", nullable=false)
   */
  private $mapping;

  /**
   * abstract
   * events in datasets may don't have abstract
   *
   * @ORM\Column(type="string", name="type", nullable=false)
   */
  private $type;

  /**
   *  Conference associated to this mappingFile
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="mappingFiles", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
  protected $conference;

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
   * Set mapping
   *
   * @param string $mapping
   *
   * @return MappingFile
   */
  public function setMapping($mapping)
  {
    $this->mapping = $mapping;

    return $this;
  }

  /**
   * Get mapping
   *
   * @return string
   */
  public function getMapping()
  {
    return $this->mapping;
  }

  /**
   * Set type
   *
   * @param string $type
   *
   * @return MappingFile
   */
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
   * @return MappingFile
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