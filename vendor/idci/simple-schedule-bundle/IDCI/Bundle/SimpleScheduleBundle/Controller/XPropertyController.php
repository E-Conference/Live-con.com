<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\SimpleScheduleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity;
use IDCI\Bundle\SimpleScheduleBundle\Form\XPropertyType;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Event controller.
 *
 * @Route("/xproperty")
 */
class XPropertyController extends Controller
{
    /**
     * Add a XProperty
     *
     * @Route("/{id}/add", name="schedule_xproperty_add")
     */
    public function addXPropertyAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $calendarEntity = $em->getRepository('IDCISimpleScheduleBundle:CalendarEntity')->find($id);

        if (!$calendarEntity) {
            throw $this->createNotFoundException('Unable to find Calendar entity.');
        }

        $entity = new XProperty();
        $form = $this->createForm(new XPropertyType, $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
        } else {
            die('todo: flash message');
        }

        return $this->redirect($this->generateUrl(
            'schedule_event_edit',
            array('id' => $calendarEntity->getId())
        ));
    }
}
