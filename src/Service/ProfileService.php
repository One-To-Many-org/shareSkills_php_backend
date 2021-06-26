<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileService implements ProfileServiceInterface
{

    const ACCEPT='Accept';
    const CONTENT_TYPE='Content-Type';
  private $em;
  private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer,RessourceOwnerService $ownerService)
    {
        $this->em= $em;
        $this->serializer=$serializer;
        /**
         * @var User $user
         */
        $user=$this->em->getRepository (User::class)->findOneBy (['id'=>22]);

        if($ownerService->isOwner ($user) && $ownerService->isAdmin ($user)){
            var_dump ('Il est propriétaire');
        } else{
            var_dump ('Il n\'est pas propriétaire');
        }
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this -> em;
    }


    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): ProfileService
    {
        $this -> em = $em;
        return $this;
    }


    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this -> serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this -> serializer = $serializer;
    }

    public function create(Request $request){
        $contentType=$request->headers->get (self::CONTENT_TYPE);
        $deserializeFormat=$this->getTypeFromApplicattion ($contentType);
        $accept= $request->headers->get (self::ACCEPT);
        $serializeFormat=$this->getTypeFromApplicattion ($accept);
    }

    public function update(Request $request, $id, $context = [])
    {
        $contentType=$request->headers->get (self::CONTENT_TYPE);
        $deserializeFormat=$this->getTypeFromApplicattion ($contentType);
        $accept= $request->headers->get (self::ACCEPT);
        $serializeFormat=$this->getTypeFromApplicattion ($accept);
    }

    public function read(Request $request, $id, $context = [])
    {
        // TODO: Implement read() method.
    }

    /**
     * @param Request $request
     * @param array $context
     * @return string
     */
    public function all(Request $request, $context = [])
    {
        $accept= $request->headers->get (self::ACCEPT);
        $serializeFormat=$this->getTypeFromApplicattion ($accept);
        $users=$this->em->getRepository (User::class)->findAll ();
        return $this->prepareAll ($users,$serializeFormat,['groups'=>'short_user']);

    }

    public function delete($id, $context = [])
    {
        // TODO: Implement delete() method.
    }

    protected function getTypeFromApplicattion($type){
        return trim(str_replace ("application/","",$type));
   }

    /**
     * @param $users
     * @param $type
     * @param array $context
     * @return string
     */
   private function prepareAll($users, $type, array $context=[]){

           switch ($type) {
               case 'xml':
                   $defaultContext=[xmlEncoder::ROOT_NODE_NAME=>'profile'];
                   $profiles=[];
                   foreach ($users as $profile){
                       /**
                        * @ met un attribut dans le champs
                        * # insère sans surcouche de balise
                        */
                       $value=["@id"=>$profile->getId (),"#"=>$profile];
                       array_push ($profiles,$value);
                   }
                   $result=$this->serializer->serialize (['numfound'=>count ($profiles),'profile-list'=>['profile'=>$profiles]],'xml',array_merge($defaultContext,$context));
                   break;
               case 'json':
                   $result=$this->serializer->serialize (['numfound'=>count ($users),'profile-list'=>$users],'json',$context);
                   break;
               default:
                   throw new \Exception("Unrecognized Format ".$type."for profile Serialisation");
           }

           return $result;
   }
}
