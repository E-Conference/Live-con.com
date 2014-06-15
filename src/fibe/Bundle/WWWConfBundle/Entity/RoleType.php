<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a role for a person in an event
 *
 *
 * @ORM\Table(name="role_type")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\RoleTypeRepository")
 *
 */
class RoleType
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
   *
   * @ORM\Column(type="string", name="name", nullable=false)
   */
  protected $name;

  /**
   * label
   *
   *
   * @ORM\Column(type="string", name="label", nullable=false)
   */
  protected $label;

  /**
   * role
   * Role how have this type
   *
   * @ORM\OneToMany(targetEntity="Role", mappedBy="type")
   */
  private $roles;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->role = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Add role
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Role $role
   *
   * @return RoleType
   */
  public function addRole(\fibe\Bundle\WWWConfBundle\Entity\Role $role)
  {
    $this->role[] = $role;

    return $this;
  }

  /**
   * Remove role
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Role $role
   */
  public function removeRole(\fibe\Bundle\WWWConfBundle\Entity\Role $role)
  {
    $this->role->removeElement($role);
  }

  /**
   * Get role
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRoles()
  {
    return $this->roles;
  }

  /**
   * __toString method
   *
   * @return mixed
   */
  public function __toString()
  {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return RoleType
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
   * Set label
   *
   * @param string $label
   *
   * @return RoleType
   */
  public function setLabel($label)
  {
    $this->label = $label;

    return $this;
  }

  /**
   * Get label
   *
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
}