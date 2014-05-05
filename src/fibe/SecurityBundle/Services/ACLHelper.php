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
   * - be registered in the ACLEntityNameArray array
   * - belongs to the namespace defined in ACL_ENTITIES_CLASSPATH const
   */
  class ACLHelper
  {

    const ACL_ENTITIES_CLASSPATH = 'fibe\\Bundle\\WWWConfBundle\\Entity';
    const LINK_WITH = 'WwwConf';
        /** @const */
    public static $ACLEntityNameArray = array('WwwConf','ConfEvent','Location','Paper','Person','Role','Organization','Topic','Module');

    protected $securityContext;
    protected $entityManager;
    protected $aclProvider;

    public function __construct(SecurityContext $securityContext,EntityManager $entityManager,MutableAclProvider $aclProvider)
    {
      $this->securityContext = $securityContext;
      $this->entityManager   = $entityManager;
      $this->aclProvider     = $aclProvider;
    }
    /**
     * Examples
     * $entity = $this->get('fibe_security.acl_helper')->getEntityACL('CREATE','Topic');
     * $entity = $this->get('fibe_security.acl_helper')->getEntityACL('EDIT','Person',$id);
     */
    public function getEntityACL($action,$repositoryName,$id=null){
        $currentConf = $this->getCurrentConf();
        if($id)
        {
          $findOneByArgs = array('id' => $id);
          if($repositoryName != ACLHelper::LINK_WITH)
          {
            $findOneByArgs['conference'] = $currentConf;
          }
          $entity = $this->entityManager->getRepository('fibeWWWConfBundle:'.$repositoryName)->findOneBy($findOneByArgs);
        }else{
          $className = $this->getClassNameByRepositoryName($repositoryName);
          $entity = new $className();
        }
        if (!$entity)
        {
            throw new NotFoundHttpException('Cannot find '.$repositoryName.' '.($id?'#'.$id:''));
        }
        if (false === $this->securityContext->isGranted($action, $entity))
        {
            throw new AccessDeniedException('You don\'t have the authorization to perform "'.$action.'" on '.$repositoryName.' '.($id?'#'.$id:''));
        }
        return $entity;
    }

    /**
     * Examples
     * $entity = $this->get('fibe_security.acl_helper')->getEntitiesACL('EDIT','Topic');
     */
    public function getEntitiesACL($action,$repositoryName)
    {
      $ids = $this->aclProvider->getAllowedEntitiesIds($this->getClassNameByRepositoryName($repositoryName), $action);
      $queryBuilder = $this->entityManager->getRepository('fibeWWWConfBundle:'.$repositoryName)->createQueryBuilder('entity');
      if($repositoryName != ACLHelper::LINK_WITH)
      {
        $this->restrictQueryBuilderByConferenceId($queryBuilder);
      }
      $this->restrictQueryBuilderByIds($queryBuilder,$ids);

      if(is_null($queryBuilder))return array();

      //TODO dig into performance issues
      $entitees = $queryBuilder->getQuery()->getResult();
      $rtn = array();
      foreach ($entitees as $entity) {
        if (true === $this->securityContext->isGranted($action, $entity))
        {
            $rtn[] = $entity;
        }
      }
      return $rtn;
    }




    private function getCurrentConf()
    {
      //TODO redirect to dashboard url with parameter
      return $this->securityContext->getToken()->getUser()->getCurrentConf();
    }

    private function restrictQueryBuilderByConferenceId(QueryBuilder $queryBuilder)
    {
      $queryBuilder->andWhere("entity.conference = ".$this->getCurrentConf()->getId());
    }

    private function restrictQueryBuilderByIds(QueryBuilder $queryBuilder,$ids)
    {
      if (is_string($ids)) {
       $queryBuilder->andWhere("entity.id IN ($ids)");
      }
      // No ACL found: deny all
      elseif ($ids===false) {
       $queryBuilder->andWhere("entity.id = 0");
      }
      elseif ($ids===true) {
         // Global-class permission: allow all
      }
    }

    private function getClassNameByRepositoryName($repositoryName)
    {
      return ACLHelper::ACL_ENTITIES_CLASSPATH.'\\'.$repositoryName;
    }
  }