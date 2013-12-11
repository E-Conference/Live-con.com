<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Form\PaperType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * Paper controller.
 * @Route("/paper")
 */
class PaperController extends Controller
{
    /**
     * Lists all Paper entities.
     * @Route("/", name="schedule_paper")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        $conf = $this->getUser()->getCurrentConf();
        $entities = $conf->getPapers()->toArray();

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
            'authorized' => $authorization->getFlagconfDatas(),
        );
    }

    /**
     * Creates a new Paper entity.
     * @Route("/create", name="schedule_paper_create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity  = new Paper();
        $form = $this->createForm(new PaperType($this->getUser()), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_paper', array('id' => $entity->getId())));
        }

        return $this->render('fibeWWWConfBundle:Paper:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Displays a form to create a new Paper entity.
     * @Route("/new", name="schedule_paper_new")
     * @Template()
     *
     */
    public function newAction()
    {
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity = new Paper();
        $form   = $this->createForm(new PaperType($this->getUser()), $entity);

        return $this->render('fibeWWWConfBundle:Paper:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Finds and displays a Paper entity.
     * @Route("/{id}/show", name="schedule_paper_show")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Paper:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),        ));
    }

    /**
     * Displays a form to edit an existing Paper entity.
     * @Route("/{id}/edit", name="schedule_paper_edit")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $editForm = $this->createForm(new PaperType($this->getUser()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Paper:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Edits an existing Paper entity.
     * @Route("/{id}/update", name="schedule_paper_update")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PaperType($this->getUser()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_paper', array('id' => $id)));
        }

        return $this->render('fibeWWWConfBundle:Paper:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Deletes a Paper entity.
     * @Route("/{id}/delete", name="schedule_paper_delete")
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
            $entity =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Paper entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_paper'));
    }

    /**
     * Creates a form to delete a Paper entity by id.
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
