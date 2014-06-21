<?php

namespace fibe\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CorsListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {   
        $request = $event->getRequest();

        $event->getResponse()->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
        $event->getResponse()->headers->set('Access-Control-Allow-Origin', '*');
        $event->getResponse()->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE OPTIONS');
    }   
}