<?php
  namespace fibe\SecurityBundle\Listener;

  use Doctrine\ORM\Event\LifecycleEventArgs; 
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use fibe\SecurityBundle\Services\ACLHelper;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
  use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
  use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity; 
  use Symfony\Component\Security\Acl\Permission\MaskBuilder;

  class AddACL {
    protected $container;

    public function __construct(ContainerInterface $container)
    {
      $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
      $entity = $args->getEntity();
      $entityManager = $args->getEntityManager();
      $token = $this->container->get('security.context')->getToken();
      if (isset($token)) {
        $user = $token->getUser();
      }
      $ACLEntityNameArray = ACLHelper::$ACLEntityNameArray; 
      if (in_array($this->get_real_class($entity), $ACLEntityNameArray)) {
        // creating the ACL
        $aclProvider = $this->container->get('security.acl.provider');

        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl); 
      } 
    }
    /**
     * Obtains an object class name without namespaces
     */
    function get_real_class($obj) {
        $classname = get_class($obj);
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
  }