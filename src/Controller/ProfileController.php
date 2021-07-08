<?php


namespace App\Controller;


use App\Controller\Handler\FormatHandlerController;
use App\Entity\User;
use App\Service\FileUploader;
use App\Service\ProfileService;
use App\Service\ProfileServiceInterface;
use App\Service\PutRequestParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController implements FormatHandlerController
{
   private $profileService;

    public function __construct(ProfileServiceInterface $profileService)
    {
      $this->profileService=$profileService;
    }

    /**
     * renvoie tout avec un groupe guest ou un groupe avec moins de données et pourquoi pas avec possibilities de paginer
     * @Route("/profiles", name="user_profile",methods="GET")
     */
    public function index(Request $request){
        $data= $this->profileService->all ($request);
        return new Response($data,Response::HTTP_OK);
    }

    /**
     * si tu est propriétaire tu vois tout (groupOwner) sinon tu vois une partie groupe (Guest)
     *  @Route("/profiles/{id}", name="show_profile",methods="GET")
     */
   public function show(Request $request,$id){
       $data=$this->profileService->read ($request,$id);
       $response=new Response($data,Response::HTTP_OK);
       return $response;
   }

    /**
     * accessible aux anonyme
     * cascade persiste
     * @Route("/profiles", name="new_profile",methods="POST")
     */
   public function new(Request $request){
       $data=$this->profileService->create ($request);
       return new Response($data,Response::HTTP_CREATED);
   }

    /**
     * il faut être propriétaire ou admin
     * @Route("/profiles/{id}", name="update_profile",methods="PUT")
     */
   public function edit(Request $request,$id,PutRequestParser $parser){
       $parser->parseTo_POST_IfMultipart ($request);
       $data=$this->profileService->update  ($request,$id);
       return new Response($data,Response::HTTP_OK);
   }

    /**
     * propriétaires ou admin
     * @Route("/profiles/{id}", name="delete_profile",methods="DELETE")
     */
   public function delete($id){
       $this->profileService->delete ($id);
       return new Response("",Response::HTTP_OK);
   }

    /**
     * si tu est propriétaire tu vois tout (groupOwner) sinon tu vois une partie groupe (Guest)
     *  @Route("/profiles/{id}/picture", name="add_picture_to_profile",methods="POST")
     */
    public function picture(Request $request, $id,FileUploader $uploader,EntityManagerInterface $em){

        /**
         * @var User $user
         */

        $user=$em->getRepository (User::class)->findOneBy (['id'=>$id]);
        $response=new Response("user not found ");
        if($user){
            $uploader->addFileTorequest ($request,$request->getContent ());
            /**
             * @var ProfileService $profileService
             */
            $profileService= $this->profileService;
            $profileService->handlePicture ($request,$user);
            $em->persist ($user);
            $em->flush ();
            $filename=$user->getPicturesPath ();
            $response->headers->set('Content-type',mime_content_type($filename));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            $response->headers->set('Content-length', filesize($filename));
            $response->headers->set ('Content-Disposition','attachment; filename="'.$filename.'"');
            $response->setContent(file_get_contents($filename));
        }

        return $response;
    }
}
