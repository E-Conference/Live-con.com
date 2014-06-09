<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Topic;
use fibe\Bundle\WWWConfBundle\Form\TopicType;
//Filter form type
use fibe\Bundle\WWWConfBundle\Form\Filters\TopicFilterType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Topic controller.
 *
 * @Route("/topic")
 */
class TopicController extends Controller
{
  /**
   * Lists all Topic entities.
   *
   * @Route("/", name="schedule_topic")
   * @Method("GET")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'Topic');
    // $entities = $this->getUser()->getCurrentConf()->getTopics()->toArray();

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

    $filters = $this->createForm(new TopicFilterType($this->getUser()));

    return array(
      'pager'        => $pager,
      'filters_form' => $filters->createView(),
    );
  }


  /**
   * Filter paper index list
   * @Route("/filter", name="schedule_topic_filter")
   */
  public function filterAction(Request $request)
  {
    $conf = $this->getUser()->getCurrentConf();
    //Filters
    $filters = $this->createForm(new TopicFilterType($this->getUser()));
    $filters->submit($request);

    if ($filters->isValid())
    {
      // bind values from the request
      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('fibeWWWConfBundle:Topic')->filtering($filters->getData(), $conf);
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
        'fibeWWWConfBundle:Topic:list.html.twig',
        array(
          'pager'    => $pager,
          'nbResult' => $nbResult,
        )
      );
    }
  }

  /**
   * Creates a new Topic entity.
   *
   * @Route("/", name="schedule_topic_create")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Topic:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Topic');
    $form = $this->createForm(new TopicType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity->setConference($this->getUser()->getCurrentConf());
      $em->persist($entity);
      $em->flush();

      //$this->get('fibe_security.acl_entity_helper')->createACL($entity,MaskBuilder::MASK_OWNER);

      return $this->redirect($this->generateUrl('schedule_topic'));
    }

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Displays a form to create a new Topic entity.
   *
   * @Route("/new", name="schedule_topic_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Topic');
    $form = $this->createForm(new TopicType(), $entity);

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
    );
  }

  /**
   * Finds and displays a Topic entity.
   *
   * @Route("/{id}", name="schedule_topic_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Topic', $id);

    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Displays a form to edit an existing Topic entity.
   *
   * @Route("/{id}/edit", name="schedule_topic_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Topic', $id);

    $editForm = $this->createForm(new TopicType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Edits an existing Topic entity.
   *
   * @Route("/{id}", name="schedule_topic_update")
   * @Method("PUT")
   * @Template("fibeWWWConfBundle:Topic:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Topic', $id);

    $editForm = $this->createForm(new TopicType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('schedule_topic_show', array('id' => $id)));
  }

  /**
   * Deletes a Topic entity.
   *
   * @Route("/{id}", name="schedule_topic_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Topic', $id);

    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->remove($entity);
      $em->flush();
      $this->container->get('session')->getFlashBag()->add(
        'success',
        'Topic successfully deleted !'
      );
    }

    return $this->redirect($this->generateUrl('schedule_topic'));
  }

  /**
   * Creates a form to delete a Topic entity by id.
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
