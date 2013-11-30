<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Form\WwwConfType;

use IDCI\Bundle\SimpleScheduleBundle\Form\XPropertyType; 
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Event; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Location; 

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * Link controller.
 *
 * @Route("/admin/conference")
 */
class ConferenceController extends Controller
{
/**
 * @Route("/edit", name="schedule_conference_edit")
 * 
 * @Template()
 */
    public function editAction(Request $request)
    {

      $em = $this->getDoctrine()->getManager();       
      $wwwConf = $this->getUser()->getCurrentConf();

      //main conf event MUST have a location
      $mainConfEvent = $wwwConf->getMainConfEvent(); 

      $form = $this->createForm(new WwwConfType($this->getUser()), $wwwConf);
       
      $request = $this->get('request');
      if ($request->getMethod() == 'POST') {
        $form->bind($request);
     
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wwwConf);
            $em->flush();

            $this->container->get('session')->getFlashBag()->add(
                'success',
                'The conference has been successfully updated'
            );
        }else{

            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Submition error, please try again.'
            );
        }
      }
      return array(
          'location' => $mainConfEvent->getLocation(),
          'wwwConf'  => $wwwConf,
          'form'     => $form->createView()
      );
    }
/**
 * @Route("/{id}/empty", name="schedule_conference_empty") 
 */
    public function emptyAction(Request $request,$id)
    {
      $em = $this->getDoctrine()->getManager();

      $conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);

      //TODO CSRF TOKEN
      // $csrf = $this->get('form.csrf_provider'); //Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider by default
      // $token = $csrf->generateCsrfToken($intention); //Intention should be empty string, if you did not define it in parameters
      // BOOLEAN $csrf->isCsrfTokenValid($intention, $token);

      //TODO CHECK RIGHT super_admin
      
      //check if the processed conference belongs to the user
      $conferences = $this->getUser()->getConferences()->toArray();
      if (!in_array($conference, $conferences)) {
          throw new AccessDeniedException('Look at your conferences !!!');
      } 
 

      $emptyConf = $this->get('emptyConf');
      $emptyConf->emptyConf($conference,$em);

      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
                'success',
                'conference successfully emptied.'
            );
      return $this->redirect($this->generateUrl('schedule_conference_edit'));
    }
}
