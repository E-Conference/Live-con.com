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


/**
 * Link controller.
 *
 * @Route("/admin/conference")
 */
class ConferenceController extends Controller
{
/**
 * @Route("/edit", name="schedule_conference_edit")
 * @Template()
 */
    public function editAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();       
      $wwwConf = $this->getUser()->getCurrentConf();
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
          'wwwConf' => $wwwConf,
          'form' => $form->createView()
      );
    }

  /**
 * @Route("/downloadLogo", name="schedule_conference_logo_download")
 * @Template()
 */
   /* public function download1Action($id=null)
   {
      $em = $this->getDoctrine()->getEntityManager();
      $doc = $em->find('MonBundle:Document',$id);
      $fichier = $doc->getPath();
 
      $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', "application/$format"); 
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $fichier, $format)); 
        $response->setCharset('UTF-8');
 
        // prints the HTTP headers followed by the content
        $response->send();
        return $response;
 
  }*/
      
}
