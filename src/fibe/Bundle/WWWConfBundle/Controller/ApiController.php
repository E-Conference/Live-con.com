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
   * @Route("/api/")
   */
  class ApiController extends Controller
  { 
  }
