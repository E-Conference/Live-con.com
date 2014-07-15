<?php

namespace fibe\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

//return json error instead of symfony's error
class SerializeExceptionListener
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // $format = $event->getRequest()->getRequestFormat();
        // if(!$format || $format === "html"){
        //     return;
        // }
        // $error = $event->getException();
        // $data = array('error' => $error->getMessage());
        // $content = $this->getSerializer()->serialize($data, $format);
        // $response = new Response($content, 400);
        // if(method_exists($error,'getStatusCode')){
        //     $response->setStatusCode($error->getStatusCode());
        // }
        // $event->setResponse($response);
    }
}