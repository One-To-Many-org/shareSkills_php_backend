<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Entity\Section;
use App\Entity\Skills;
use App\Entity\Training;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use App\Service\MediaTypesService;
use App\Service\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="test")
     */
    public function index(SectionRepository $rep,EntityManagerInterface $em): Response
    {
       // $all= $em->getRepository (Section::class)->findAll ();
       // $allExperiences= $em->getRepository (Experience::class)->findAll ();
       // $allTraining= $em->getRepository (Trainning::class)->findAll ();
        $allTraining= $em->getRepository (Skills::class)->findAll ();
        $context=[
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::ATTRIBUTES => ['id','Fields','status','description', 'Level' => ['id','description']]
        ];
        return $this->json ($allTraining,200,[],$context);
    }

    /**
     * @Route("/users/short", name="test_short_users")
     */
    public function short_users(EntityManagerInterface $em): Response
    {
        $users= $em->getRepository (User::class)->findAll ();

        $context=[
            'groups'=>'short_user'
        ];
        return $this->json ($users,200,[],$context);
    }

    /**
     * @Route("/users/full", name="test_users")
     */
    public function full_users(EntityManagerInterface $em,\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tkInt): Response
    {
        $token = $tkInt->getToken();

            /** @var User $user */
            $user = $token->getUser();

        $users= $em->getRepository (User::class)->findAll ();

        $context=[
            'groups'=>'full_user'
        ];
        return $this->json ($users,200,[],$context);
    }


    /**
     * @Route("/users/new", name="test_new", methods="POST")
     */
    public function new_user(EntityManagerInterface $em,Request $req,SerializerInterface $serializer): Response
    {
        $data=$req->getContent ();
        $user=$serializer->deserialize ($data,User::class,'json');
        $em->persist ($user);
        $em->flush ();
        $data=$serializer->serialize ($user,'json');

        return $this->json ($data,200,[]);
    }

    /**
     * marche parce que la methode est post et $_POSTexiste ne marchera pas avec PUT
     * @Route("/test/{id}/test", name="post_picture_profile",methods="POST")
     */
    public function test(Request $request, $id,UserRepository $uRep,EntityManagerInterface $em,ProfileService $profileService){

        /**
         * @var User $user
         */

        $user=$uRep->findOneBy (['id'=>$id]);
        $data = $request->get ('data');


        /**
         * @Var UploadedFile $file
         */
        $file = $request->files->get ('picture');
        $user->setPicture ($file);
        $form=$this->createForm (UserType::class,$user);
        $form->submit (json_decode ($data,true),false);
        $profileService->handleRelations ($user);
        $response=new Response("user not found ");
        if($user){
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

    /**
     *  @Route("/test/{id}/picture", name="test_picture_profile_send",methods="POST")
     */
    public function send(Request $request, $id, MediaTypesService $mapper, EntityManagerInterface $em){

        /**
         * @var User $user
         */

        $user=$em->getRepository (User::class)->findOneBy (['id'=>$id]);
        $response=new Response("user not found ");
        if($user){
            $contentType=mime_content_type($request->getContent (true));
            $fileExtension=$mapper->guessExtension ($contentType);
            $filename=$user->getFirstName ().$user->getId ().uniqid ().$request->headers->get ('filename').$fileExtension;
            $picturePath='profiles/pictures/'.$filename;
            file_put_contents ($picturePath,$request->getContent ());
            $user->setPicture (new File($picturePath));
            $user->setFilename ($filename);
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

    /**
     *  @Route("/read/{id}/picture", name="read_picture_profile",methods="GET")
     */
    public function read($id,UserRepository $uRep){

        /**
         * @var User $user
         */
        $user=$uRep->findOneBy (['id'=>$id]);
        $response=new Response("user not found ");
        if($user){
            $filename=$user->getPicturesPath ();
            // $response->headers->set('Content-type',mime_content_type($filename));
            // $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            //  $response->headers->set('Content-length', filesize($filename));
            //  $response->headers->set ('Content-Disposition','attachment; filename="'.$filename.'"');
            $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_INLINE);
            $response->setContent(file_get_contents($filename));
        }

        return $response;
    }
}
