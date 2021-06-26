<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ErrorController extends AbstractController
{
    /**
     * @Route("/badrequest", name="bad_request")
     * @param string $message
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function badRequest(SerializerInterface $serializer,string $message='An error is occured'):Response{
        /**
         * beaucoup de galaire avec les erreurs call has on null ici avec $this->json() car en effet en instanciant moi même ce cntroleurs
         * je n'ai pas mis le container. Le container de abstract controller étant null celà génère plein d'erreurs. Utiliser
         * new Response est la stratégie de retourner une réponse quand on a un container vide pour faire render() ou json() ...
         */
            return new Response($serializer->serialize (['message'=>$message],'json'),Response::HTTP_BAD_REQUEST,['Content-type'=>'json']);
    }
}
