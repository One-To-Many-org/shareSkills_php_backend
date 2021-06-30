<?php


namespace App\Controller;


use App\Controller\Handler\FormatHandlerController;
use App\Entity\User;
use App\Form\UserType;
use App\Service\ProfileServiceInterface;
use App\Service\RessourceOwnerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
       return new Response($data,Response::HTTP_OK);
   }

    /**
     * accessible aux anonyme
     * cascade persiste
     * @Route("/profiles", name="new_profile",methods="POST")
     */
   public function new(Request $request){
       $data=$this->profileService->create ($request);
       return new Response($data,Response::HTTP_OK);
   }

    /**
     * il faut être propriétaire ou admin  methods="GET"
     * @Route("/profiles/{id}", name="update_profile",methods="POST")
     */
   public function edit(Request $request,$id){

       $data=$this->profileService->update  ($request,$id,['form'=>$this->createForm (UserType::class)]);
       return new Response($data,Response::HTTP_OK);
   }

    /**
     * propriétaires ou admin
     */
   public function delete(){

   }
}
