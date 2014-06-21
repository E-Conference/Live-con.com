<?php

  namespace fibe\MobileAppBundle\Controller;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

  use fibe\MobileAppBundle\Entity\MobileAppConfig;
  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

  /**
   * Mobile app controller.
   *
   * @Route("/apps")
   */
  class MobileAppPublicController extends Controller
  {
    /**
     * @Route("/rest/{slug}",name="mobileAppPublic_index")
     * @Template("fibeMobileAppBundle:MobileAppPublic:angularIndex.html.twig")
     */
    public function indexAction($slug)
    {
      $em = $this->getDoctrine()->getManager();

    	$conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->findOneBySlug($slug);
    	$mobile_app_config = $conference->getAppConfig();
        $apiUri = $this->get('router')->generate('idci_exporter_api_homeapi');
        $apiType = "rest";
        $baseUri = "http://data.live-con.com/resource/conference/" . $conference->getId() . "/" . $conference->getSlug();
        return array(
            'api_uri' => $apiUri,
            'api_type' => $apiType,
            'paper_module' => $conference->getModule()->getPaperModule(),
            'organization_module' => $conference->getModule()->getOrganizationModule(),
            'conference_baseUri' => $baseUri,
            'mobile_app_config' => $mobile_app_config,
            'conference' => $conference,
        );
    }

    /**
     * @Route("/{slug}",name="mobileAppPublic_sparql_index")
     * @Template("fibeMobileAppBundle:MobileAppPublic:angularIndex.html.twig")
     */
    public function indexSparqlAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->findOneBySlug($slug);

        if($conference == null) {
            throw new NotFoundHttpException();
        }

        $mobile_app_config = $conference->getAppConfig();
        $apiUri = $this->get('router')->generate('idci_exporter_api_homeapi');
        $apiType = "rest";
        $baseUri = "http://data.live-con.com/resource/conference/" . $conference->getId() . "/" . $conference->getSlug();

       

        return array(
            'api_uri' => $apiUri,
            'api_type' => $apiType,
            'paper_module' => $conference->getModule()->getPaperModule(),
            'organization_module' => $conference->getModule()->getOrganizationModule(),
            'conference_baseUri' => $baseUri,
            'mobile_app_config' => $mobile_app_config,
            'conference' => $conference,
        );
        /*
        $em = $this->getDoctrine()->getManager();

        $conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->findOneBySlug($slug);
        if(!$conference){
            throw new NotFoundHttpException();
        }
        $mobile_app_config = $conference->getAppConfig();
        $baseUri = "http://data.live-con.com/resource/conference/".$conference->getId()."/".$conference->getSlug();
        $apiUri = "http://data.live-con.com/sparql";
        $apiType = "sparql";
        return array(
            'api_uri' => $apiUri,
            'api_type' => $apiType,
            'paper_module' => $conference->getModule()->getPaperModule(),
            'organization_module' => $conference->getModule()->getOrganizationModule(),
            'conference_baseUri' => $baseUri,
            'mobile_app_config' => $mobile_app_config,
            'conference' => $conference,
        );*/
    }
  }
