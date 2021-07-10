<?php


namespace App\EventSuscriber;


use App\Controller\Handler\FormatHandlerController;
use App\Service\MediaTypesService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestFormatHandler implements EventSubscriberInterface
{
    protected static $supportsFormat=['image/png'=>'png', '*/*'=>'json','application/json'=>'json','application/xml'=>'xml','application/yaml'=>'yaml','application/*'=>'json','multipart/form-data'=>''];
    protected static $manyAcceptResolver=['*/*'=>'application/json','application/*'=>'application/json'];
    const NO_CONTENT_TYPE_MESSAGE="You don't provide in request headers your data content type provided it in your request header with the keys \"Content-Type\" or \"Send-Type\" Like this by example"
    ."\"Content-Type\"=>\"appliction/json\"";
    const NO_SUPPORTED_SEND_TYPE="Your content-type %s is not supported";
    const NO_SUPPORTED_RETURN_TYPE="You don't provide valid accepted for the support format are :   %s verifie that you have correctly type the accepted-type or return-ty without space";
    const ACCEPT='Accept';
    const CONTENT_TYPE='Content-Type';
    public static $isFormatHandlerController;
    /**
     * @var MediaTypesService
     */
    protected $mediaTypeService;

    public function __construct(MediaTypesService $mediaTypesService){
        $this->mediaTypeService=$mediaTypesService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
           // RequestEvent::class=>'onKernelRequest',
            ResponseEvent::class=>'onKernelResponse',
            ControllerEvent::class=>'onKernelController',

        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {

    }

    /**
     * On essaie de resoudre le content-type et le accept type au besoin
     * et on redirige vers ErrorController quand on estime que la requête est mal faite.
     * En gros la règle c'est quand on evoie des données il faut nous spécifié le type de donnée pour qu'on sache si on les supporte
     * Si on doit recevoir des données il faut spécifier un accept type que nous supportons.
     * Au cas ou le accept n'est pas spécifier c'est application/json  qui est pris par défaut
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {

        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof FormatHandlerController ) {
            /**
             * pour récupérer une erreur détecté dans la requête et rediriger vers le controller des erreurs avec le message
             */
            $errorMessage = "";

            $request=$event->getRequest();

            /**
             * S'il est entrain d'envoyer des donnée il faut obligatoirement spécifier le content-type et le content-type doit être supporté
             */
            if($this->isSendingData ($request)) {

                $contentType = $request ->getContentType ();

                if (empty( $contentType )) {
                    $errorMessage = self::NO_CONTENT_TYPE_MESSAGE.$this->getAcceptFormatMessage ();
                }else{
                    if (!$this -> isSupportedFormat ( $contentType )) {
                        $errorMessage = sprintf ( self::NO_SUPPORTED_SEND_TYPE.$this->getAcceptFormatMessage (), $contentType );
                    }
                }
            }

            /**
             * S'il doit recevoir une donnée il faut un accept par défaut on lui met application/json
             */
            if (empty($errorMessage) && $this->willReceiveData ($request)){
                    $accept=$this->resolveAccept ($request);
                    if(empty($accept)){
                        if(count ($request->getAcceptableContentTypes ())){
                            $errorMessage=sprintf (self::NO_SUPPORTED_RETURN_TYPE,$this->getSupportFormatAsString ());
                        }else{
                            $accept='application/json';
                        }
                    }
                   $request->headers->set (self::ACCEPT,$accept);
            }
            if(!empty($errorMessage)){
                $this->redirectToErrorController ($event,$errorMessage);
            }

        }

    }

    /**
     * Si le content type de la réponse n'est pas définit dans la réponse on le définit par le accept résolue
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $accepts = $event->getRequest ()->getAcceptableContentTypes ();

        if(count ($accepts)<2){
            $contentType=$event->getResponse ()->headers->get (self::CONTENT_TYPE);
            if(empty($contentType)){
                $contentType=$event->getRequest ()->headers->get (self::ACCEPT);
                if($event->getResponse ()->isSuccessful () && empty($contentType)){
                    $event->getResponse ()->headers->set (self::CONTENT_TYPE,$contentType);
                }
            }
        }

    }

    /**
     * @param $format
     * @return bool
     */
    public function isSupportedFormat($format): bool
    {
        $format=preg_replace  ('/;\sboundary=(.*)|charset=(.*)/',"",$format);
        return array_key_exists  ($format,self::$supportsFormat);
    }

    public function isSendingData(Request $request){
        return $request->isMethod ('PUT')||$request->isMethod ('POST') || $request->isMethod ('PATCH') ;
    }

    public function willReceiveData(Request $request){
        return !$request->isMethod ('DELETE');
    }

    public function getSupportFormatAsString(){
        return   "[".implode (" , ",array_keys (self::$supportsFormat))."]";
    }

    public function getAcceptFormatMessage(){
        return  " We accepted " .$this->getSupportFormatAsString ();
    }

    public function redirectToErrorController(ControllerEvent $event,$message){
        $controllerResolver= new ControllerResolver();
            $request=$event->getRequest ();
            $request->attributes->add (['_route'=>'bad_request',"_controller" => "App\Controller\ErrorController::badRequest","message"=>$message]);
            $callable=$controllerResolver->getController ($request);
            $event->setController ($callable);
            $event->stopPropagation ();

    }

    /**
     * return one Accept type who is support
     * if any accept is not support or they do't provide any accept
     * it will return empity string
     * @param AcceptHeader $acceptHeader
     * @return string
     */
    public function resolveAccept(Request $request){
        // $acceptHeaderPourTester='text/plain;q=0.5, text/html, text/*;q=0.8, */*;q=0.3',application/xml;q=0.9,application/json;q=0. doit retourner application/xml avec $acceptHeaderPourTester
       $accept=  $this->mediaTypeService->resolveAcceptWith ($request,array_keys  (self::$supportsFormat),true,false);
       return  array_key_exists ($accept,self::$manyAcceptResolver)?self::$manyAcceptResolver[$accept]:$accept;

    }


}
