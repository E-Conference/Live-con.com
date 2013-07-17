<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api controller.
 *
 * redirects :
 *     http://calendar.labs.idci.fr/api/schedule?uri=FOOBAR&PARAMS[]
 * to :
 *     http://calendar.labs.idci.fr/api/schedule_event.jsonp?xproperty_value=FOOBAR&PARAMS[]
 *
 * @Route("/api/schedule")
 */
class ApiController extends Controller
{
/**
 * @Route("", name="wwwconf_api_event") 
 */
    public function apiAction(Request $request)
    { 
/*
        $response = $this->forward('AcmeHelloBundle:Hello:fancy', array(
            'xproperty_value'  => $request->request->get('uri', '')
        ));

    // ... further modify the response or return it directly

    return $response;
*/
        
	    $em = $this->getDoctrine()->getManager();
	    $query = $request->query;
	    $uriParam = $query->get('uri', '');
	    if($uriParam!='')
	    {
	        $query->add(array( "xproperty_value" => $uriParam));
	        $query->remove("uri");
	    }
        $entities = $em->getRepository('IDCISimpleScheduleBundle:CalendarEntity')->extract($query->all()); 
        $result = $this->get('idci_exporter.manager')->export($entities,"jsonp"); 
        $response = new Response();
        $response->setContent($result->getContent());
        $response->headers->set('Content-Type', $result->getContentType()); 
        return $response;
    }
    
    
}
