<?php

  namespace fibe\SecurityBundle\Controller;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller; 
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Component\HttpFoundation\RedirectResponse;
  /**
   * Override edit and show action of FOS\UserBundle\Controller\ProfileController
   */
  class EnrichAccountController extends Controller
  {


    /**
     * set user Id in the session so we can get it back next to his social account loggin
     * @Route("/enrich/{socialService}", name="enrich_account") 
     */
    public function enrichAccountAction($socialService)
    { 
      $session = $this->get('session');
      $session->set('userId',$this->getUser()->getId());
      $oAuthHelper = $this->get('hwi_oauth.templating.helper.oauth');
      return new RedirectResponse($oAuthHelper->getLoginUrl($socialService)); 
    }
  }
