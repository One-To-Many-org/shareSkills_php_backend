<?php


namespace App\Service;


use App\Entity\Field;
use App\Entity\Level;
use App\Entity\User;
use App\Exceptions\CustomException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileService implements ProfileServiceInterface
{

    const ACCEPT='Accept';
    const CONTENT_TYPE='Content-Type';
    const OWNER_GROUPS='full_user';
    const GUEST_GROUPS="short_user";
  private $em;
  private $serializer;
  private $ownerService;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer,RessourceOwnerService $ownerService)
    {
        $this->em= $em;
        $this->serializer=$serializer;
        $this->ownerService=$ownerService;
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
        $contentFormt=$this->getContentFormat ($request->headers);
        $acceptFormat=$this->getAcceptedFormat ($request->headers);
        $data=$request->getContent ();
        /**
         * @var User $profile
         */
        $profile=$this->serializer->deserialize ($data,User::class,$contentFormt);

        $ownSkills=$profile->getOwnSkills ();
        $searchedSkills=$profile->getSearchedSkills ();
        $skills= array_merge ($ownSkills->toArray (),$searchedSkills->toArray ());

        foreach ($skills as $skill){
            $lDescrition=$skill->getLevelDescription ();
            $fDescription=$skill->getFieldDescription ();

            /**
             * @var Level $level
             */
            $level=$this->em->getRepository (Level::class)->findOneBy (["description"=>$lDescrition]);
            /**
             * @var Field $field
             */
            $field=$this->em->getRepository (Field::class)->findOneBy (["description"=>$fDescription]);
            $skill->setLevel ($level);
            $skill->setField ($field);
        }

        $this->em->persist ($profile);
        $this->em->flush();
        $data=$this->serializer->serialize ($data,$acceptFormat,['groups'=>self::OWNER_GROUPS]);

        return $data;

    }

    public function update(Request $request, $id, $context = [])
    {
        $contentFormat=$this->getContentFormat ($request->headers);
        $acceptFormat=$this->getAcceptedFormat ($request->headers);
        /**
         * @var User $profile
         */
        $profile=$this->em-> getRepository (User::class) ->findOneBy (['id'=>$id]);
        $data=$request->getContent ();
        /*
         * @var User $profile
         */
        $profile = $this->serializer ->deserialize (trim ($data),User::class,$contentFormat,[AbstractNormalizer::OBJECT_TO_POPULATE => $profile,AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true]);

        $skills= array_merge ($profile->getOwnSkills ()->toArray (),$profile->getSearchedSkills ()->toArray ());

        foreach ($skills as $skill){
            $lDescrition=$skill->getLevelDescription ();
            $fDescription=$skill->getFieldDescription ();

            /**
             * @var Level $level
             */
            $level=$this->em->getRepository (Level::class)->findOneBy (["description"=>$lDescrition]);
            /**
             * @var Field $field
             */
            $field=$this->em->getRepository (Field::class)->findOneBy (["description"=>$fDescription]);
            $skill->setLevel ($level);
            $skill->setField ($field);
        }

        $this->em->persist ($profile);
        $this->em->flush ();
        //je resérialise l'objet qui vient être créer juste pour le retourner à l'utilisateur
        $data = $this->serializer->serialize($profile, $acceptFormat);
        return $data;
    }

    public function read(Request $request, $id, $context = [])
    {
        $acceptFormat=$this->getAcceptedFormat ($request->headers);
        /**
         * @var User $user
         */
        $user= $this->em->getRepository (User::class)->findOneBy (['id'=>$id]);
        if ($user && $user instanceof User){
            $groups=$this->ownerService->isOwner ($user)?self::OWNER_GROUPS:self::GUEST_GROUPS;
            $data=$this->serializeOne ($user,$acceptFormat,['groups'=>$groups]);
            return $data;
        }

        throw new CustomException("Fail to load user with id =".$id);
    }

    /**
     * @param Request $request
     * @param array $context
     * @return string
     */
    public function all(Request $request, $context = [])
    {
        $acceptFormat=$this->getAcceptedFormat ($request->headers);
        $users=$this->em->getRepository (User::class)->findAll ();
        return $this->serializeList ($users,$acceptFormat,['groups'=>'short_user']);

    }

    public function delete($id, $context = [])
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param $userId
     * @return string
     */
    public function getGroup($userId){
        /**
         * @var User $user
         */
        $user= $this->em->getRepository (User::class)->findOneBy (['id'=>$userId]);
        $groups=$this->ownerService->isOwner ($user)?self::OWNER_GROUPS:self::GUEST_GROUPS;
        return $groups;
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
   private function serializeList($users, $type, array $context=[]){

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
                   throw new CustomException("Unrecognized Format ".$type."for profile Serialisation");
           }

           return $result;
   }

    private function serializeOne(User $user, $type, array $context=[]){

        $context=array_merge ($context,[]);
        switch ($type) {
            case 'xml':
                $defaultContext=[xmlEncoder::ROOT_NODE_NAME=>'profile'];
                $result=$this->serializer->serialize ($user,'xml',array_merge($defaultContext,$context));
                break;
            case 'json':
                $result=$this->serializer->serialize ($user,'json',$context);
                break;
            default:
                throw new CustomException("Unrecognized Format ".$type."for profile Serialisation");
        }
        return $result;
    }

   public function getContentFormat(HeaderBag $headers){
       $contentType=$headers->get (self::CONTENT_TYPE);
       return $this->getTypeFromApplicattion ($contentType);
   }

    public function getAcceptedFormat(HeaderBag $headers){
        $acceptType=$headers->get (self::ACCEPT);
        return $this->getTypeFromApplicattion ($acceptType);
    }
}
/**
 * AbstractNormalizer::OBJECT_TO_POPULATE n'est utilisé que pour l'objet de niveau supérieur.
 * Si cet objet est la racine d'une arborescence, tous les éléments enfants qui existent dans les données normalisées seront recréés avec de nouvelles instances.
 * Lorsque l'option AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE est définie sur true, les enfants existants de la racine OBJECT_TO_POPULATE sont mis à jour à partir des données normalisées, au lieu que le dénormaliseur les recrée.
 * Notez que DEEP_OBJECT_TO_POPULATE ne fonctionne que pour les objets enfants uniques, mais pas pour les tableaux d'objets. Ceux-ci seront toujours remplacés lorsqu'ils sont présents dans les données normalisées.
 */
