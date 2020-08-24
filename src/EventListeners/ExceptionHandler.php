<?php

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class ExceptionHandler implements EventSubscriberInterface{


    /**
     * @LoggerIterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleEntityException', 1],
                ['handle404Exception', 0],
                ['handleGenericException']
            ],
        ];
    }

    public function handle404Exception(GetResponseForExceptionEvent $event){
        
        if($event->getException() instanceof NotFoundHttpException){

            $response = HypermidiaResponse::fromError($event->getException())->getResponse();
            $event->setResponse($response);
        }
    }

    public function handleEntityException(GetResponseForExceptionEvent $event){

        if($event->getException() instanceof EntityFactoryException){
            $response = HypermidiaResponse::fromError($event->getException())
                ->getResponse();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    public function handleGenericException(
        GetResponseForExceptionEvent $event)
        {
            $this->logger->critical('Uma exceção ocorre. {stack}',
                ['stack' => $event->getException()->getTraceAsString()
            ]);

            $response = HypermidiaResponse::fromError($event->getException());
            $event->setResponse($response->getResponse());
        }

}