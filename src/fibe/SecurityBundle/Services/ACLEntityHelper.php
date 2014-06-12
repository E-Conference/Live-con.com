<?php
namespace fibe\SecurityBundle\Services;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use fibe\SecurityBundle\Entity\ConfPermission;
use fibe\SecurityBundle\Entity\UserConfPermission;

/**
 * to be used with this class entity must :
 * - have a link to the conference table
 * - be registered in the here present public static $ACLEntityNameArray
 * 
 *  Explaination on the role table
 *     http://symfony.com/fr/doc/current/cookbook/security/acl_advanced.html#table-de-permission-integree
 */
class ACLEntityHelper extends ACLHelper
{

  const LINK_WITH = 'WwwConf';

  /** @const */
  public static $ACLEntityNameArray = array(
    'WwwConf' => array(
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Team' => array(
      'classpath' => 'fibe\\SecurityBundle\\Entity',
    ),
    'MobileAppConfig' => array(
      'classpath' => 'fibe\\MobileAppBundle\\Entity',
    ),
    'Module' => array(
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'MobileAppConfig' => array(
      'classpath' => 'fibe\\MobileAppBundle\\Entity'
    ),

    'ConfEvent' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Location' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Paper' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Person' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Role' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Organization' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Topic' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Sponsor' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'SocialServiceAccount' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Category' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'Equipment' => array(
      'parent'    => 'getConference',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    ),
    'RoleType' => array(
      'parent'    => 'WwwConf',
      'classpath' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    )
  );


  /**
   * get an entity in the conf with permission check
   * i.e.
   *   $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE','Topic');
   *   $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT','Person',$id);
   *   $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT','Person',$entity);
   */
  public function getEntityACL($action, $repositoryName, $entity = null)
  {

    if (!is_object($entity))
    {
      $entity = $this->getEntityInConf($repositoryName, $entity);
    }
    if (false === $this->securityContext->isGranted($action, $entity))
    {
      throw new AccessDeniedException(
        sprintf(
          ACLHelper::NOT_AUTHORYZED_ENTITY_LABEL,
          $action,
          $repositoryName,
          '#' . $entity->getId()
        )
      );
    }

    return $entity;
  }

  /**
   * get entities link with the current conf with their foreign key "conference" = current_conf_id
   * get every WwwConf when repositoryName param is = "WwwConf"
   * i.e.
   * $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('EDIT','Topic');
   * TODO : perf improvments
   */
  public function getEntitiesACL($action, $repositoryName)
  { 
    // $ids = $this->aclProvider->getAllowedEntitiesIds($this->getClassNameByRepositoryName($repositoryName), $action);
    $queryBuilder = $this->entityManager->getRepository('fibeWWWConfBundle:' . $repositoryName)->createQueryBuilder(
      'entity'
    );

    if (is_null($queryBuilder))
    {
      return array();
    }

    if ($repositoryName != ACLEntityHelper::LINK_WITH)
    {
      $this->restrictQueryBuilderByConferenceId($queryBuilder);
    }
    // $this->restrictQueryBuilderByIds($queryBuilder, $ids);

    $entities = $queryBuilder->getQuery()->getResult();
    if("VIEW" == $action && $repositoryName != ACLEntityHelper::LINK_WITH) 
    {
      return $entities;
    }
    
    $rtn = array();
    foreach ($entities as $entity)
    {
      if (true === $this->securityContext->isGranted($action, $entity))
      {
        $rtn[] = $entity;
      }
    }

    return $rtn;
  }


  public function getACEByRepositoryName($repositoryName, $user = null, $id = null)
  { 
    $entity = $this->getEntityInConf($repositoryName, $id);  
    return $this->getACEByEntity($entity,$user);
  }

  /**
   * [getACEByEntity description]
   *
   * @param  [type] $entity     [description]
   * @param  [type] $user       [description]
   * @param  string $returnType all|mask|index|action (all | int binary mask | index of the ace in the acl | readable action i.e. VIEW)
   * @param  [type] $acl        provide acl if you already got it
   *
   * @return [string|int]       the uppest permission
   */
  public function getACEByEntity($entity, $user = null, $returnType = "action", $acl = null)
  {
    $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
    $userSecurityIdentity = UserSecurityIdentity::fromAccount($user ? $user : $this->getUser());
    if (!$acl)
    {
      $acl = $this->aclProvider->findAcl(
        $entitySecurityIdentity,
        array($userSecurityIdentity)
      );
    }
    //find the ace for the given user
    foreach ($acl->getObjectAces() as $index => $ace)
    {
      if ($ace->getSecurityIdentity()->equals($userSecurityIdentity))
      {
        switch ($returnType)
        {
          case 'all':
            return array(
              'mask'   => $ace->getMask(),
              'index'  => $index,
              'action' => $this->getMask($ace->getMask())
            );
          case 'mask':
            return $ace->getMask();
          case 'index':
            return $index;
          case 'action':
          default:
            return $this->getMask($ace->getMask());
        }
      }
    }
    throw new NoAceFoundException(
      sprintf(
        'Cannot find ACE %s %s for user %s',
        get_class($entity),
        '#' . $entity->getId(),
        $user ? $user->getUsername() : "[current user]"
      )
    );
  }


  /**
   * lookup in ACLEntityHelper::$ACLEntityNameArray to return the full class path
   *
   * @param  [String] $repositoryName           registered in the ACLEntityHelper::$ACLEntityNameArray
   *
   * @return [String]                           the full class path
   * @throw  [EntityACLNotRegisteredException]  in case entity is not registered in the array
   */
  public function getClassNameByRepositoryName($repositoryName)
  { 
    if (!isset(self::$ACLEntityNameArray[$repositoryName]))
    {
      throw new EntityACLNotRegisteredException(
        "Can't get ACL for Entity [" . $repositoryName . "] as it's not registered in ACLEntityHelper::\$ACLEntityNameArray"
      );
    }

    return self::$ACLEntityNameArray[$repositoryName]['classpath'] . '\\' . $repositoryName;
  }
 
  public static function getRepositoryNameByClassName($className)
  { 
    $class = new \ReflectionClass($className); 

    if (!isset(self::$ACLEntityNameArray[$class->getShortName()]))
    {
      throw new EntityACLNotRegisteredException(
        "Can't get ACL for Entity [" . $className . "] as it's not registered in ACLEntityHelper::\$ACLEntityNameArray"
      );
    }

    return $class->getShortName();
  }


  /**
   * filter by conferenceId if the repository != this::LINK_WITH
   *
   * @param  [type] $repositoryName [description]
   * @param  [type] $id             [description]
   *
   * @return [type]                 [description]
   */
  private function getEntityInConf($repositoryName, $id = null)
  {
    $entity = null;
    if ($id)
    {
      $findOneByArgs = array('id' => $id);
      if ($repositoryName != ACLEntityHelper::LINK_WITH)
      {
        $findOneByArgs['conference'] = $this->getCurrentConf();
      }
      $entity = $this->entityManager->getRepository($this->getClassNameByRepositoryName($repositoryName))->findOneBy(
        $findOneByArgs
      );
    }
    else
    {
      $className = $this->getClassNameByRepositoryName($repositoryName);
      $entity = new $className();
    }
    if (!$entity)
    {
      $this->throwNotFoundHttpException($repositoryName, $id);
    }

    return $entity;
  }

}


class EntityACLNotRegisteredException extends \RunTimeException
{
}