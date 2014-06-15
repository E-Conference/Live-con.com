<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Organization;
use fibe\Bundle\WWWConfBundle\Form\OrganizationType;

//Filter form type
use fibe\Bundle\WWWConfBundle\Form\Filters\OrganizationFilterType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Organization controller.
 *
 * @Route("/organization")
 */
class OrganizationController extends Controller
{
  /**
   * Lists all Organization entities.
   *
   * @Route("/", name="schedule_organization_index")
   * @Method("GET")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'Organization');
    // $entities = $this->getUser()->getCurrentConf()->getOrganizations()->toArray();

    $adapter = new ArrayAdapter($entities);
    $pager = new PagerFanta($adapter);
    $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

    try
    {
      $pager->setCurrentPage($request->query->get('page', 1));
    } catch (NotValidCurrentPageException $e)
    {
      throw new NotFoundHttpException();
    }

    //Filters Form
    $filters = $this->createForm(new OrganizationFilterType($this->getUser()));

    return array(
      'pager'        => $pager,
      'filters_form' => $filters->createView(),
    );
  }

  /**
   * Filter organization index list
   * @Route("/filter", name="schedule_organization_filter")
   */
  public function filterAction(Request $request)
  {

    $em = $this->getDoctrine()->getManager();

    $conf = $this->getUser()->getCurrentConf();
    //Filters
    $filters = $this->createForm(new OrganizationFilterType($this->getUser()));
    $filters->submit($request);

    if ($filters->isValid())
    {
      // bind values from the request

      $entities = $em->getRepository('fibeWWWConfBundle:Organization')->filtering($filters->getData(), $conf);
      $nbResult = count($entities);

      //Pager
      $adapter = new ArrayAdapter($entities);
      $pager = new PagerFanta($adapter);
      $pager->setMaxPerPage($this->container->getParameter('max_per_page'));
      try
      {
        $pager->setCurrentPage($request->query->get('page', 1));
      } catch (NotValidCurrentPageException $e)
      {
        throw new NotFoundHttpException();
      }

      return $this->render(
        'fibeWWWConfBundle:Organization:list.html.twig',
        array(
          'pager'    => $pager,
          'nbResult' => $nbResult,
        )
      );
    }

  }

  /**
   * Creates a new Organization entity.
   *
   * @Route("/create", name="schedule_organization_create")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Organization:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Organization');
    $form = $this->createForm(new OrganizationType($this->getUser()), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity->setConference($this->getUser()->getCurrentConf());

      foreach ($entity->getMembers()
               as
               $person)
      {
        $person->addOrganization($entity);
        //$entity->addMember($person);
        $em->persist($person);
      }

      $em->persist($entity);
      $em->flush();

      //$this->get('fibe_security.acl_entity_helper')->createACL($entity,MaskBuilder::MASK_OWNER);

      return $this->redirect($this->generateUrl('schedule_organization_index'));
    }

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Displays a form to create a new Organization entity.
   *
   * @Route("/new", name="schedule_organization_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Organization');
    $form = $this->createForm(new OrganizationType($this->getUser()), $entity);

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Finds and displays a Organization entity.
   *
   * @Route("/{id}/show", name="schedule_organization_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Organization', $id);

    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Displays a form to edit an existing Organization entity.
   *
   * @Route("/{id}/edit", name="schedule_organization_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Organization', $id);

    $editForm = $this->createForm(new OrganizationType($this->getUser()), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Edits an existing Organization entity.
   *
   * @Route("/{id}/update", name="schedule_organization_update")
   * @Method("PUT")
   * @Template("fibeWWWConfBundle:Organization:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Organization', $id);

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new OrganizationType($this->getUser()), $entity);

    $em = $this->getDoctrine()->getManager();

    $personToRemove = $entity->getMembers();
    foreach ($personToRemove
             as
             $person)
    {
      $person->removeOrganization($entity);
      $entity->removeMember($person);
      $em->persist($person);
    }

    $editForm->bind($request);
    $personToAdd = $entity->getMembers();
    if ($editForm->isValid())
    {

      //Add members selected in forms to the current organization thank to the woning sir
      foreach ($personToAdd
               as
               $person)
      {
        $person->addOrganization($entity);
        //$entity->addMember($person);
        $em->persist($person);
      }

      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('schedule_organization_index'));
    }

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Deletes a Organization entity.
   *
   * @Route("/{id}/delete", name="schedule_organization_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Organization', $id);
      $em = $this->getDoctrine()->getManager();
      //The object must belong to the current conf
      $currentConf = $this->getUser()->getCurrentConf();
      $em->remove($entity);
      $em->flush();
      $this->container->get('session')->getFlashBag()->add(
        'success',
        'Organization successfully deleted !'
      );
    }

    return $this->redirect($this->generateUrl('schedule_organization_index'));
  }

  /**
   * Creates a form to delete a Organization entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(array('id' => $id))
      ->add('id', 'hidden')
      ->getForm();
  }
}
