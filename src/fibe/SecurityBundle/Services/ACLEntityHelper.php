<?php
namespace fibe\SecurityBundle\Services;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use fibe\SecurityBundle\Entity\ConfPermission;
use fibe\SecurityBundle\Entity\UserConfPermission;

/**
 * entity must to be used with this class :
 * - have a link to the conference table
 * - be registered in the here present public static $ACLEntityNameArray
 */
class ACLEntityHelper extends ACLHelper
{

  const LINK_WITH = 'WwwConf';

  /** @const */
  public static $ACLEntityNameArray = array(
    'WwwConf' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Team' => 'fibe\\SecurityBundle\\Entity',
    'MobileAppConfig' => 'fibe\\MobileAppBundle\\Entity',
    'Module' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'ConfEvent' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Location' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Paper' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Person' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Role' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Organization' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Topic' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Sponsor' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'SocialServiceAccount' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Category' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'Equipment' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'RoleType' => 'fibe\\Bundle\\WWWConfBundle\\Entity',
    'MobileAppConfig' => 'fibe\\MobileAppBundle\\Entity'
  );


  /**
   * get an entity with permission check
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

    //check if action is correct
    $this->getMask($action);

    //check permission
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
   * Examples
   * $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('EDIT','Topic');
   */
  public function getEntitiesACL($action, $repositoryName)
  {
    //TODO : fix this to don't waste time to loop over each entities
    $ids = $this->aclProvider->getAllowedEntitiesIds($this->getClassNameByRepositoryName($repositoryName), $action);
    $queryBuilder = $this->entityManager->getRepository('fibeWWWConfBundle:' . $repositoryName)->createQueryBuilder(
      'entity'
    );
    if ($repositoryName != ACLEntityHelper::LINK_WITH)
    {
      $this->restrictQueryBuilderByConferenceId($queryBuilder);
    }
    $this->restrictQueryBuilderByIds($queryBuilder, $ids);

    if (is_null($queryBuilder))
    {
      return array();
    }

    $entities = $queryBuilder->getQuery()->getResult();

    if("VIEW" == $action && $repositoryName != ACLEntityHelper::LINK_WITH) 
    {
      return $entities;
    }
    
    $rtn = array();
    //TODO : fix getAllowedEntitiesIds to don't waste time to loop over each entities
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
   * @param  string $returnType mask|index|action (int binary mask | index of the ace in the acl | readable action i.e. VIEW)
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
    throw new AceNotFoundException(
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
    $ACLEntityNameArray = ACLEntityHelper::$ACLEntityNameArray;
    if (!isset($ACLEntityNameArray[$repositoryName]))
    {
      throw new EntityACLNotRegisteredException(
        "Can't get ACL for Entity [" . $repositoryName . "] as it's not registered in ACLEntityHelper::\$ACLEntityNameArray"
      );
    }

    return $ACLEntityNameArray[$repositoryName] . '\\' . $repositoryName;
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

class AceNotFoundException extends \Exception
{
}


class EntityACLNotRegisteredException extends \RunTimeException
{
}