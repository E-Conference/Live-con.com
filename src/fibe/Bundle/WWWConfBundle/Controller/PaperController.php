<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Form\PaperType;

/**
 * Paper controller.
 *
 */
class PaperController extends Controller
{
    /**
     * Lists all Paper entities.
     * @Route("/paper", name="schedule_paper")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $conf = $this->getUser()->getCurrentConf();
        //$entities = $em->getRepository('fibeWWWConfBundle:Paper')->findAll();
        $entities = $conf->getPapers();


        return $this->render('fibeWWWConfBundle:Paper:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Paper entity.
     * @Route("/create", name="schedule_paper_create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $entity  = new Paper();
        $form = $this->createForm(new PaperType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_paper_show', array('id' => $entity->getId())));
        }

        return $this->render('fibeWWWConfBundle:Paper:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
        $entity = new Paper();
        $form   = $this->createForm(new PaperType(), $entity);

        return $this->render('fibeWWWConfBundle:Paper:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Paper entity.
     * @Route("/{id}/show", name="schedule_paper_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Paper')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Paper:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Paper entity.
     * @Route("/{id}/edit", name="schedule_paper_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Paper')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $editForm = $this->createForm(new PaperType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Paper:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Paper entity.
     * @Route("/{id}/update", name="schedule_paper_update")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Paper')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Paper entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PaperType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_paper_show', array('id' => $id)));
        }

        return $this->render('fibeWWWConfBundle:Paper:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Paper entity.
     * @Route("/{id}/delete", name="schedule_paper_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:Paper')->find($id);

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
