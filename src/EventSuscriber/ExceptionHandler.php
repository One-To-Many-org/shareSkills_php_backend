<?php


namespace App\EventSuscriber;


use App\Exceptions\CustomException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionHandler implements EventSubscriberInterface
{
   protected $serialiser;

    public function __construct(serializerInterface $serializer){
        $this->serialiser=$serializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION=>[['onCustomException', 10],['onRoutingException', 0],['onKernelException', -10]]
        ];
    }

    public function onKernelException(ExceptionEvent $event){

        $exception=$event->getThrowable ();
        $response = new Response();
        $data=['message'=>'An Error is occured on the server while processing request'.$exception->getMessage ()];
        $response->setContent($this->serialiser->serialize ($data,'json'));
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        var_dump (get_class ($exception));
       // $event->setResponse($response);
    }

    public function onCustomException(ExceptionEvent $event){
        $exception=$event->getThrowable ();
        $response = new Response();
        /**
         * setter la reponse dans le if pour que les autres methods soit appelé si c'est pas une instance de CustomException
         */
        if($exception instanceof CustomException){
            $data=['message'=>'An Error is occured on the server while processing request','developperMessage'=>$exception->getCustomMessage ()];
            $response->setContent($this->serialiser->serialize ($data,'json'));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    public function onRoutingException(ExceptionEvent $event){
        $exception=$event->getThrowable ();
        $response = new Response();
        if($exception instanceof MethodNotAllowedHttpException || $exception instanceof  InvalidParameterException
                           || $exception instanceof NotFoundHttpException ||
                                            $exception instanceof  ResourceNotFoundException || $exception instanceof  MissingMandatoryParametersException){

            $data=['message'=>'An Error is occured on the server while processing request','developperMessage'=>$exception->getMessage ()];
            $response->setContent($this->serialiser->serialize ($data,'json'));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }

    }
}
