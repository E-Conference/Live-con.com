<?php
namespace fibe\SecurityBundle\Services;

use Symfony\Component\Routing\RouterInterface,
  Symfony\Component\HttpFoundation\RedirectResponse,
  Symfony\Component\HttpFoundation\Request,
  Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface,
  Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class ChooseConferenceSuccessHandler implements AuthenticationSuccessHandlerInterface
{
  protected $router;

  public function __construct(RouterInterface $router)
  {
    $this->router = $router;
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token)
  {
    if (!$token->getUser()->getCurrentConf())
    {
      return new RedirectResponse($this->router->generate('dashboard_index'));
    }
    else
    {
      return new RedirectResponse($this->router->generate('schedule_conference_show'));
    }
  }
}