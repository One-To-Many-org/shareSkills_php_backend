<?php


namespace App\Service;


use App\Entity\Field;
use App\Entity\Level;
use App\Entity\OwnSkill;
use App\Entity\SearchedSkill;
use App\Entity\Section;
use App\Entity\Skills;
use App\Entity\User;
use App\Exceptions\CustomException;
use App\Form\OwnSkillType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileService implements ProfileServiceInterface
{

    const ACCEPT='Accept';
    const CONTENT_TYPE='Content-Type';
    const OWNER_GROUPS='full_user';
    const GUEST_GROUPS="short_user";
    const DATA_FIELD='data';
    private $em;
    private $serializer;
    private $ownerService;
    private $typeMapper;
    private $formFactory;
    private $normalizer;

    /**
     * @var User $user
     */
    private static $user;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer,RessourceOwnerService $ownerService,
                                MimeTypeMapperService $typeMapper,FormFactoryInterface $formFactory, NormalizerInterface $normalizer)
    {
        $this->em= $em;
        $this->serializer=$serializer;
        $this->ownerService=$ownerService;
        $this->typeMapper=$typeMapper;
        $this->formFactory=$formFactory;
        $this->normalizer=$normalizer;
          /**
         * @var User $user
         */
        $user=$this->em->getRepository (User::class)->findOneBy (['id'=>22]);
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

    /**
     * @return MimeTypeMapperService
     */
    public function getTypeMapper(): MimeTypeMapperService
    {
        return $this -> typeMapper;
    }

    public function create(Request $request){
        $contentFormat=$this->getContentFormat ($request);
        $acceptFormat=$this->getAcceptedFormat ($request);

        if($this->isMultipartFormData ($request)){
             $profile=$this->newWithFormSubmit ($this->toArrayContent ($request));
             $this->handlePicture ($request,$profile);
        }else{
           $profile= $this->newWithSerializer ($contentFormat,$request->getContent ());
        }
        $profile=$this->handleRelations ($profile);
        $this->em->persist ($profile);
        $this->em->flush();
        $data=$this->serializer->serialize ($profile,$acceptFormat,['groups'=>self::OWNER_GROUPS]);
        return $data;
    }

    public function update(Request $request, $id, $context = [])
    {

        $acceptFormat=$this->getAcceptedFormat ($request);
        /**
         * @var User $profile
         */
        $profile=$this->loadUser ($id);
        /**
         * @var FormInterface $form
         */
        $form=$this->formFactory->create (UserType::class,$profile);
        $arrayOfContent= $this->toArrayContent ($request);
        $form->submit($arrayOfContent,false);
        /**
         * @var User $profile
         */
        $profile=$form->getData ();
        $this->handlePicture ($request,$profile);
        $this->handleRelations ($profile);
        $this->em->persist ($profile);
        $this->em->flush ();
        //je resérialise l'objet qui vient d'être créer juste pour le retourner à l'utilisateur
      //  $data = $this->serializer->serialize($profile, $acceptFormat,['groups'=>self::OWNER_GROUPS]);
        $data=$this->serializeOne ($profile,$acceptFormat,['groups'=>self::OWNER_GROUPS]);
        return $data;
    }

    public function read(Request $request, $id, $context = [])
    {
        $acceptFormat=$this->getAcceptedFormat ($request);
        /**
         * @var User $user
         */
        $user= $this->loadUser ($id);
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
        $acceptFormat=$this->getAcceptedFormat ($request);
        $users=$this->em->getRepository (User::class)->findAll ();
        return $this->serializeList ($users,$acceptFormat,['groups'=>'short_user']);

    }

    public function delete($id, $context = [])
    {
        // TODO: Implement delete() method.
    }

    /**
     * cas contentype est de type mime : json, xml,...
     * @param $contentFormat
     * @param string | array $data
     * @return User
     */
    protected function newWithSerializer($contentFormat,$data){
        /**
         * @var User $profile
         */
        $profile=$this->serializer->deserialize ($data,User::class,$contentFormat);
        $profile=$this->handleRelations ($profile);
        return $profile;
    }

    /**
     * cas content-type est multipart avec un champ data et picture
     * @param array $data
     * @return User
     */
    protected function newWithFormSubmit(array $data){
        $profile=new User();
        $form=$this->formFactory->create (UserType::class,$profile);
        $form->submit($data,false);
        /**
         * @var User $profile
         */

        $profile=$form->getData ();
        $profile=$this->handleRelations ($profile);
       return $profile;
    }

    /**
     * cas données du formulaire envoyé par un navigateur
     * @param Request $request
     * @return User
     */
    protected function newWithFormHandle(Request $request){
        $profile=new User();
        $form=$this->formFactory->create (UserType::class,$profile);
        $form->handleRequest ($request);
        /**
         * @var User $profile
         */

        $profile=$form->getData ();
        $profile=$this->handleRelations ($profile);
        return $profile;
    }

    public function handlePicture(Request $request,User $profile){
        /**
         * @Var UploadedFile $file
         */
        $picture = $request->files->get ('picture');
        if($picture){
            $profile->setPicture ($picture);
        }
        return $profile;
    }

    /**
     * @param User $profile
     * @return User
     */
    protected function handleSKills(User $profile){
        $skills= array_merge ($profile->getOwnSkills ()->toArray (),$profile->getSearchedSkills ()->toArray ());
        foreach ($skills as $skill){
            /**
             * @var OwnSkill |SearchedSkill $skill
             */
            if(empty($skill->getField ())){
                $lDescrition=$skill->getLevelDescription ();
                /**
                 * @var Level $level
                 */
                $level=$this->em->getRepository (Level::class)->findOneBy (["description"=>$lDescrition]);
                $skill->setLevel ($level);
            }
            if(empty($skill->getField ())){
                $fDescription=$skill->getFieldDescription ();
                /**
                 * @var Field $field
                 */
                $field=$this->em->getRepository (Field::class)->findOneBy (["description"=>$fDescription]);
                $skill->setField ($field);
            }

            if (empty($skill->getUser())){
                $skill->setUser($profile);
            }

        }
        return $profile;
    }


    protected function handleSections(User $profile){
        $sections= array_merge ($profile->getTrainings ()->toArray (),$profile->getExperiences ()->toArray ());

        foreach ($sections as $section){
            /**
             * @var  Section $section
             */
            if(empty($section->getUser ())){
                $section->setUser ($profile)  ;
            }

        }
        return $profile;
    }

    /**
     * @param User $profile
     * @return User
     */
    public function handleRelations(User $profile){
        $this->handleSKills ($profile);
        $this->handleSections ($profile);
        return $profile;
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
                   throw new CustomException("Unrecognized Format ".$type."for profile list Serialisation");
           }

           return $result;
   }

    private function serializeOne(User $user, $type, array $context=[]){
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

   protected function getContentFormat(Request $request){

       if ($this->isMultipartFormData ($request)){
           if($this->isJson ($request->get (self::DATA_FIELD))){
               return 'json';
           }
           if($this->isXML  ($request->get (self::DATA_FIELD))){
               return 'xml';
           }
           throw new CustomException("Unable to decode your data");
       }
       $contentype=$request->headers->get (self::CONTENT_TYPE);
       $format= $this->typeMapper->guessExtension ($contentype);

       return $format;

   }

    /**
     * @param Request $request
     * @return bool
     */
   protected  function isMultipartFormData(Request $request){
       $contentype=$request->headers->get (self::CONTENT_TYPE);
      return $this->typeMapper->isMultipartFormData ($contentype);
   }

    protected function getAcceptedFormat(Request $request){
        return $this->typeMapper->guessExtension ($request->headers->get (self::ACCEPT));
    }

    /**
     * @param $userId
     * @return string
     */
    protected function getUSerSerializeGroup($userId){
        /**
         * @var User $user
         */
        $user= $this->loadUser ($userId);
        $groups=$this->ownerService->isOwner ($user)?self::OWNER_GROUPS:self::GUEST_GROUPS;
        return $groups;
    }

    /**
     * @param $id
     * @param bool $throwException
     * at true throw exception if the user not found at false return null
     * @return User|bool
     * @throws CustomException
     */
    protected function loadUser($id,$throwException=true){
        /**
         * Dpoctrine le fait surement avec du cache mais j'en fait plus
         */
       if(!is_null (self::$user) && self::$user->getId ()===$id){
           return self::$user;
       }
        self::$user=$this->em->getRepository (User::class)->findOneBy (['id'=>$id]);
       if(is_null (self::$user) && $throwException){
           throw new CustomException("Profile not found ");
       }
       return self::$user;
     }

     protected function isXML($xmlstr){
         /**
          *  if (trim($xmlContent) == '') {
         return false;
         }

         libxml_use_internal_errors(true);

         $doc = new DOMDocument($version, $encoding);
         $doc->loadXML($xmlContent);

         $errors = libxml_get_errors();
         libxml_clear_errors();

         return empty($errors);
          */
         $doc = simplexml_load_string($xmlstr);
         return $doc===false?false:true ;
     }

    protected function isJson($string) {
       $json= json_decode($string);
       if(!$json){
           $jsonEncoder= new JsonEncoder();
           $json=$jsonEncoder->decode ($string,'json');
       }
        return $json?json_last_error() === JSON_ERROR_NONE:true;
    }

    /**
     * @param Request $request
     * @return array
     * @throws CustomException
     */
    protected function toArrayContent(Request $request):array
    {
        $contentType=$this->getContentFormat ($request);
        $data=$request->get (self::DATA_FIELD);

        if('xml'===$contentType){
            try {
                $xmlEncoder=new XmlEncoder();
                $xmlStr=!empty($data)?$data:$request->getContent ();
                $arrayContent= $xmlEncoder->decode ($xmlStr,'xml');
            }catch (\Exception $exception){
                throw new CustomException($exception->getMessage ());
            }
            return $arrayContent;
        }

        if('json'==$contentType){
            return !empty($data)? json_decode ($data,true):$request->toArray ();
        }

        throw new CustomException("unable to decode your content type");

    }
}
/**
 * AbstractNormalizer::OBJECT_TO_POPULATE n'est utilisé que pour l'objet de niveau supérieur.
 * Si cet objet est la racine d'une arborescence, tous les éléments enfants qui existent dans les données normalisées seront recréés avec de nouvelles instances.
 * Lorsque l'option AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE est définie sur true, les enfants existants de la racine OBJECT_TO_POPULATE sont mis à jour à partir des données normalisées, au lieu que le dénormaliseur les recrée.
 * Notez que DEEP_OBJECT_TO_POPULATE ne fonctionne que pour les objets enfants uniques, mais pas pour les tableaux d'objets. Ceux-ci seront toujours remplacés lorsqu'ils sont présents dans les données normalisées.
 *  // $profile = $this->serializer ->deserialize (trim ($data),User::class,$contentFormat,[AbstractNormalizer::OBJECT_TO_POPULATE => $profile,AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true]);
 */
