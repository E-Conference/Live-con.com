<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
use fibe\Bundle\WWWConfBundle\Form\ConfEventType;
use fibe\Bundle\WWWConfBundle\Entity\Role;
use fibe\Bundle\WWWConfBundle\Entity\Topic;
use fibe\Bundle\WWWConfBundle\Form\RoleType as RoleType;
use fibe\Bundle\WWWConfBundle\Form\TopicType as TopicType;

use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty;  

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ConfEvent controller.
 * @Route("/confevent")
 */
class ConfEventController extends Controller
{
    /**
     * Lists all ConfEvent entities.
     * @Route(name="schedule_confevent")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $this->getUser()->getCurrentConf()->getEvents()->toArray();

        $adapter = new ArrayAdapter($entities);
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

        try {
            $pager->setCurrentPage($request->query->get('page', 1));
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return array(
            'pager' => $pager,
        );
    }

    /**
     * Creates a new ConfEvent entity.
     *  @Route("/create", name="schedule_confevent_create")
     */
    public function createAction(Request $request)
    {
        $entity  = new ConfEvent();

        $form = $this->createForm(new ConfEventType($this->getUser()), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            //Link the new Event to the current Conf 
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity); 
            $em->flush();

       /*   $xprop= new XProperty(); 
            $xprop->setXNamespace("event_uri"); 
            $xprop->setXKey(rand(0,999999));
            $xprop->setXValue("http://dataconf-event/" . $entity->getId());  
            $xprop->setCalendarEntity($entity); 
            
            $em->persist($xprop); 

            $em->flush(); */

            return $this->redirect($this->generateUrl('schedule_confevent_show', array('id' => $entity->getId())));
        }

        return $this->render('fibeWWWConfBundle:ConfEvent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new ConfEvent entity.
     * @Route("/new",name="schedule_confevent_new") 
     * @Template()
     */
    public function newAction()
    {
        $entity = new ConfEvent();
        $form   = $this->createForm(new ConfEventType($this->getUser()), $entity);


        return $this->render('fibeWWWConfBundle:ConfEvent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ConfEvent entity.
     * @Route("/{id}/show", name="schedule_confevent_show")
     * @Template()
     */
     
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:ConfEvent:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing ConfEvent entity.
     * @Route("/{id}/edit", name="schedule_confevent_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }

        $role = new Role();
        $roleForm = $this->createForm(new RoleType($this->getUser()), $role);
        $editForm = $this->createForm(new ConfEventType($this->getUser()), $entity);

        $papersForSelect = $this->getUser()->getCurrentConf()->getPapers()->toArray();
        $form_paper = $this->createFormBuilder($entity)
            ->add('papers', 'entity', array(
                      'class'    => 'fibeWWWConfBundle:Paper',
                      'property' => 'title',
                      'required' => false,
                      'multiple' => false,
                      'choices'=> $papersForSelect,
                      'label'    => "Select paper"))
            ->getForm();

         $topicsForSelect = $this->getUser()->getCurrentConf()->getTopics()->toArray();
         $form_topic = $this->createFormBuilder($entity)
            ->add('topics', 'entity', array(
                  'class'    => 'fibeWWWConfBundle:Topic',
                  'required' => false,
                  'property' => 'name',
                  'multiple' => false,
                  'label'    => "Select topic" ))
            ->getForm();

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:ConfEvent:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'role_form'    => $roleForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'paper_form' => $form_paper->createView(),
            'topic_form' => $form_topic->createView(),
        ));
    }

    /**
     * Edits an existing ConfEvent entity.
     *  @Route("/{id}/update", name="schedule_confevent_update")
     *  @Template("fibeWWWConfBundle:ConfEvent:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }
 

        $form = $this->createForm(new ConfEventType($this->getUser()), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity); 

            $em->flush();

        }

            return $this->redirect($this->generateUrl('schedule_confevent_show', array('id' => $id)));
    
    }



       /**
     * Add topic to a confEvent
     *  @Route("/addTopic", name="schedule_confevent_addTopic")
     *  @Method("POST")
     *  
     */
    public function addTopicAction(Request $request)
    {
        $id_topic = $request->request->get('id_topic');
        $id_entity = $request->request->get('id_entity');
           
         $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_entity);
        $topic =  $em->getRepository('fibeWWWConfBundle:Topic')->find($id_topic);

        //Add paper to the confEvent
        $entity->addTopic($topic);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:topicRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }


     /**
     * Delete topic of a confEvent
     *  @Route("/deleteTopic", name="schedule_confevent_deleteTopic")
     *  @Method("POST")
     *  
     */
    public function deleteTopicAction(Request $request)
    {
        $id_topic = $request->request->get('id_topic');
        $id_entity = $request->request->get('id_entity');
           
         $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_entity);
        $topic =  $em->getRepository('fibeWWWConfBundle:Topic')->find($id_topic);

        //Add paper to the confEvent
        $entity->removeTopic($topic);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:topicRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }



     /**
     * Add paper to the confEvent
     *  @Route("/addPaper", name="schedule_confevent_addPaper")
     *  @Method("POST")
     *  
     */
    public function addPaperAction(Request $request)
    {
        $id_paper = $request->request->get('id_paper');
        $id_entity = $request->request->get('id_entity');
           
         $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_entity);
        $paper =  $em->getRepository('fibeWWWConfBundle:Paper')->find($id_paper);

        //Add paper to the confEvent
        $entity->addPaper($paper);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:paperRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }

     /**
     * Delete paper to a confEvent
     *  @Route("/deletePaper", name="schedule_confevent_deletePaper")
     *  @Method({"DELETE","POST"})
     *  
     */
    public function deletePaperAction(Request $request)
    {
        $id_paper = $request->request->get('id_paper');
        $id_entity = $request->request->get('id_entity');
           
         $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_entity);
        $paper =  $em->getRepository('fibeWWWConfBundle:Paper')->find($id_paper);

        //Add paper to the confEvent
        $entity->removePaper($paper);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:paperRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }

     /**
     * Add person to the confEvent
     * 
     *  @Route( "/addPerson",name="schedule_confevent_addPerson")
     *  @Method("POST")
     *  
     */
    public function addPersonAction(Request $request)
    {      
        $id_person = $request->request->get('id_person');
        $id_type = $request->request->get('id_type');
        $id_event = $request->request->get('id');
     
        $em = $this->getDoctrine()->getManager();

        $type = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id_type);
        $person =  $em->getRepository('fibeWWWConfBundle:Person')->find($id_person);
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_event);
      
        $role = new Role();
        $role->setPerson($person);
        $role->setType($type);
        $role->setEvent($entity);
       //TODO $role->setConference($this->getUser()->getCurrentConf());
        $em->persist($role);
        
        //Add paper to the confEvent
        $entity->addRole($role);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:personRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }

    /**
     * Delete person  to a confEvent
     *  @Route("/deletePerson", name="schedule_confevent_deletePerson")
     *  @Method("POST")
     *  
     */
    public function deletePersonAction(Request $request)
    {
        $id_role = $request->request->get('id_role');
        $id_entity = $request->request->get('id_entity');
           
         $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id_entity);
        $role =  $em->getRepository('fibeWWWConfBundle:Role')->find($id_role);

        //Add paper to the confEvent
        $entity->removeRole($role);
        $em->remove($role);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:personRelation.html.twig', array(
            'entity'  => $entity,
        ));
    }

    /**
     * Deletes a ConfEvent entity.
     * @Route("/{id}/delete", name="schedule_confevent_delete")
     * @Method({"DELETE","POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ConfEvent entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_event'));
    }

    /**
     * Creates a form to delete a ConfEvent entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
