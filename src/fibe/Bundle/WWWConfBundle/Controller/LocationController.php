<?php

/**
 *
 * @author :  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Location;
use fibe\Bundle\WWWConfBundle\Form\LocationType;
use fibe\Bundle\WWWConfBundle\Entity\Equipment;

// Filter Form
use fibe\Bundle\WWWConfBundle\Form\Filters\LocationFilterType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Location controller.
 *
 * @Route("/location")
 */
class LocationController extends Controller
{
  /**
   * Lists all Location entities.
   *
   * @Route("/", name="schedule_location")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'Location');

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

    $filters = $this->createForm(new LocationFilterType($this->getUser()));

    return array(
      'pager'        => $pager,
      'filters_form' => $filters->createView(),
    );
  }

  /**
   * Filter location index list
   * @Route("/filter", name="schedule_location_filter")
   */
  public function filterAction(Request $request)
  {

    $em = $this->getDoctrine()->getManager();

    $conf = $this->getUser()->getCurrentConf();
    //Filters
    $filters = $this->createForm(new LocationFilterType($this->getUser()));
    $filters->submit($request);

    if ($filters->isValid())
    {
      // bind values from the request

      $entities = $em->getRepository('fibeWWWConfBundle:Location')->filtering($filters->getData(), $conf);
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
        'fibeWWWConfBundle:Location:list.html.twig',
        array(
          'pager'    => $pager,
          'nbResult' => $nbResult,
        )
      );
    }
  }

  /**
   * Finds and displays a Location entity.
   *
   * @Route("/{id}/show", name="schedule_location_show")
   * @Template()
   */
  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Location', $id);

    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Displays a form to create a new Location entity.
   *
   * @Route("/new", name="schedule_location_new")
   * @Template()
   */
  public function newAction()
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Location');
    $form = $this->createForm(new LocationType(), $entity);

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Creates a new Location entity.
   *
   * @Route("/create", name="schedule_location_create")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Location:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Location');
    $form = $this->createForm(new LocationType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity->setConference($this->getUser()->getCurrentConf());
      $em->persist($entity);
      $em->flush();
      //$this->get('fibe_security.acl_entity_helper')->createACL($entity,MaskBuilder::MASK_OWNER);

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity%[%id%] has been created',
          array(
            '%entity%' => 'Location',
            '%id%'     => $entity->getId()
          )
        )
      );

      return $this->redirect($this->generateUrl('schedule_location'));
    }

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Displays a form to edit an existing Location entity.
   *
   * @Route("/{id}/edit", name="schedule_location_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Location', $id);

    $editForm = $this->createForm(new LocationType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    $equipments = $this->getDoctrine()->getManager()->getRepository(
      'fibeWWWConfBundle:Equipment'
    )->getEquipmentForLocationSelect($entity);

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      'equipments'  => $equipments
    );
  }

  /**
   * Edits an existing Location entity.
   *
   * @Route("/{id}/update", name="schedule_location_update")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Location:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Location', $id);

    $editForm = $this->createForm(new LocationType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity%[%id%] has been updated',
          array(
            '%entity%' => 'Location',
            '%id%'     => $entity->getId()
          )
        )
      );

      return $this->redirect($this->generateUrl('schedule_location'));
    }

    return array(
      'entity'    => $entity,
      'edit_form' => $editForm->createView()
    );
  }


  /**
   * Deletes a Location entity.
   *
   * @Route("/{id}/delete", name="schedule_location_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Location', $id);

    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();

      $em->remove($entity);
      $em->flush();

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity% has been deleted',
          array(
            '%entity%' => 'Location'
          )
        )
      );
    }

    return $this->redirect($this->generateUrl('schedule_location'));
  }

  /**
   * Display Location deleteForm.
   *
   * @Template()
   */
  public function deleteFormAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Location', $id);

    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
    );
  }

  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(array('id' => $id))
      ->add('id', 'hidden')
      ->getForm();
  }
}
