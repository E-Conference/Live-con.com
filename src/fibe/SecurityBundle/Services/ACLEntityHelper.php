<?php
  namespace fibe\SecurityBundle\Services;

  use Symfony\Component\Security\Core\SecurityContext; 
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\QueryBuilder;
  use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

  /**
   * entity must to be used with this class :
   * - have a link to the conference table 
   * - be registered in the here present public static $ACLEntityNameArray
   * - belongs to the namespace defined in ACL_ENTITIES_CLASSPATH const
   */
  class ACLEntityHelper extends ACLHelper
  {

    const ACL_ENTITIES_CLASSPATH = 'fibe\\Bundle\\WWWConfBundle\\Entity';
    const LINK_WITH = 'WwwConf';
    
    /** @const */
    public static $ACLEntityNameArray = array('WwwConf','ConfEvent','Location','Paper','Person','Role','Organization','Topic','Module');

    /**
     * Examples
     * $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE','Topic');
     * $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT','Person',$id);
     */
    public function getEntityACL($action,$repositoryName,$id=null){
        $entity = $this->getEntityInConf($repositoryName,$id);
        if (false === $this->securityContext->isGranted($action, $entity))
        {
            throw new AccessDeniedException(sprintf(ACLHelper::AUTHORYZED_ENTITY_LABEL,
              $action,
              $repositoryName,
              $id?'#'.$id:''
            )); 
        }
        return $entity;
    }

    /**
     * Examples
     * $entity = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('EDIT','Topic');
     */
    public function getEntitiesACL($action,$repositoryName)
    {
      $ids = $this->aclProvider->getAllowedEntitiesIds($this->getClassNameByRepositoryName($repositoryName), $action);
      $queryBuilder = $this->entityManager->getRepository('fibeWWWConfBundle:'.$repositoryName)->createQueryBuilder('entity');
      if($repositoryName != ACLEntityHelper::LINK_WITH)
      {
        $this->restrictQueryBuilderByConferenceId($queryBuilder);
      }
      $this->restrictQueryBuilderByIds($queryBuilder,$ids);

      if(is_null($queryBuilder))return array();

      //TODO dig into performance issues
      $entities = $queryBuilder->getQuery()->getResult();
      $rtn = array();
      foreach ($entities as $entity) {
        if (true === $this->securityContext->isGranted($action, $entity))
        {
            $rtn[] = $entity;
        }
      }
      return $rtn;
    }

    private function getEntityInConf($repositoryName,$id=null)
    {
      $entity = null;
      if($id)
      {
        $findOneByArgs = array('id' => $id);
        if($repositoryName != ACLEntityHelper::LINK_WITH)
        {
          $findOneByArgs['conference'] = $this->getCurrentConf();
        }
        $entity = $this->entityManager->getRepository('fibeWWWConfBundle:'.$repositoryName)->findOneBy($findOneByArgs);
      }else{
        $className = $this->getClassNameByRepositoryName($repositoryName);
        $entity = new $className();
      }
      if (!$entity)
      {
        $this->throwNotFoundHttpException( $repositoryName, $id?'#'.$id:'');
      }
      return $entity;
    }

  }