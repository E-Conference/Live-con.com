<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

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
        
        //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $entities = $user->getCurrentConf()->getTopics()->toArray();

        $adapter = new ArrayAdapter($entities);
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

        try {
            $pager->setCurrentPage($request->query->get('page', 1));
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

         $filters =$this->createForm(new TopicFilterType($this->getUser()));
        return array(
            'pager' => $pager,
            'authorized' => $authorization->getFlagconfDatas(),
            'filters_form' => $filters->createView(),
        );
    }


     /**
     * Filter paper index list
     * @Route("/filter", name="schedule_topic_filter")
     */
    public function filterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $conf = $this->getUser()->getCurrentConf();
        //Filters
        $filters =$this->createForm(new TopicFilterType($this->getUser()));
        $filters->bindRequest($this->get('request'));

        if ($filters->isValid())  {
            // bind values from the request
          
             $entities = $em->getRepository('fibeWWWConfBundle:Topic')->filtering($filters->getData(), $conf);
             $nbResult = count($entities);

             //Pager
             $adapter = new ArrayAdapter($entities);
             $pager = new PagerFanta($adapter);
             $pager->setMaxPerPage($this->container->getParameter('max_per_page'));
             try {
               $pager->setCurrentPage($request->query->get('page', 1));
             } catch (NotValidCurrentPageException $e) {
                throw new NotFoundHttpException();
             }

             return $this->render('fibeWWWConfBundle:Topic:list.html.twig', array(
                 'pager'  => $pager,
                 'nbResult' => $nbResult,
             ));
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
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity  = new Topic();
        $form = $this->createForm(new TopicType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_topic'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagSched(),
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
        
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }
        $entity = new Topic();
        $form   = $this->createForm(new TopicType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagSched(),
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
        
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagSched(),
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
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();

        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $editForm = $this->createForm(new TopicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagSched(),
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
        
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();

         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TopicType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_topic'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagSched(),
        );
    }

    /**
     * Deletes a Topic entity.
     *
     * @Route("/{id}", name="schedule_topic_delete")
     * @Method({"POST", "DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
             //The object have to belongs to the current conf
             $currentConf=$this->getUser()->getCurrentConf();
             $entity =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Topic entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_topic'));
    }

    /**
     * Creates a form to delete a Topic entity by id.
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
