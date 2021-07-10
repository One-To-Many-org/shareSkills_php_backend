<?php


namespace App\Service;


use App\Entity\Field;
use App\Entity\Level;
use App\Entity\OwnSkill;
use App\Entity\SearchedSkill;
use App\Entity\Section;
use App\Entity\User;
use App\Exceptions\CustomException;
use App\Form\UserType;
use App\Serializer\ProfileSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProfileService implements ProfileServiceInterface
{
    const OWNER_GROUPS='full_user';
    const GUEST_GROUPS="short_user";
    const DATA_FIELD='data';
    const SUPPORT=['xml','json'];
    private $em;
    private $serializer;
    private $ownerService;
    private $typeMapper;
    private $formFactory;
    private $decoder;
    private $validator;

    /**
     * @var User $user
     */
    private static $user;

    public function __construct(EntityManagerInterface $em, ProfileSerializer $serializer, RessourceOwnerService $ownerService,
                                MediaTypesService $typeMapper, FormFactoryInterface $formFactory, DataDecoder $decoder,ValidatorInterface $validator)
    {
        $this->em= $em;
        $this->serializer=$serializer;
        $this->ownerService=$ownerService;
        $this->typeMapper=$typeMapper;
        $this->formFactory=$formFactory;
        $this->decoder=$decoder;
        $this->validator=$validator;
    }

    /**
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $isMultipartRequest=$this->typeMapper->isMultipartFormDataRequest  ($request);
        $acceptFormat=$this->typeMapper->resolveAcceptWith  ($request);
        $dataField=$isMultipartRequest?self::DATA_FIELD:"";
        $data=$isMultipartRequest?$request->get (self::DATA_FIELD):$request->getContent ();

        if(!$data){
            throw new CustomException("No data provide to create user");
        }

        $contentFormat=$this->typeMapper->getContentFormat ($request,$dataField);

        if(in_array ($contentFormat,self::SUPPORT)){
            try {
                $decodeData=$this->decoder->decode ($data,$contentFormat);
                if (!is_array ($decodeData)){
                    throw new CustomException();
                }
            }catch (\Exception $exception){
                throw new CustomException("We are unable to decode your data it's not valid data ".$exception->getMessage ());
            }

        }else{
            throw new CustomException("We don't support  $contentFormat format");
        }

        $profile=$this->newWithFormSubmit ($decodeData);
        $this->handlePicture ($request,$profile);
        $this->handleRelations ($profile);
        $this->em->persist ($profile);
        $this->em->flush();
        $data=$this->serializer->serializer->serialize ($profile,$acceptFormat,['groups'=>self::OWNER_GROUPS]);
        return $data;
    }

    /**
     * @throws CustomException
     */
    public function update(Request $request, $id, $context = [])
    {
        $this->isAllowed ($id,'edit');
        $isMultipartRequest=$this->typeMapper->isMultipartFormDataRequest  ($request);
        $acceptFormat=$this->typeMapper->resolveAcceptWith  ($request);
        $dataField=$isMultipartRequest?self::DATA_FIELD:"";
        $data=$isMultipartRequest?$request->get (self::DATA_FIELD):$request->getContent ();
        if(!$data){
            throw new CustomException("No data provide to update user");
        }
        $contentFormat=$this->typeMapper->getContentFormat ($request,$dataField);
        /**
         * @var User $profile
         */
        $profile=$this->loadUser ($id);
        $form=$this->formFactory->create (UserType::class,$profile);
        if(in_array ($contentFormat,self::SUPPORT)){
            try {
                $arrayOfContent= $this->decoder->decode ($data,$contentFormat);
                if (!is_array ($arrayOfContent)){
                    throw new CustomException();
                }
            }catch (\Exception $exception){
                throw new CustomException("We are unable to decode your data it's not valid data");
            }
        }else{
            throw new CustomException("We don't support  $contentFormat format");
        }

        $form->submit($arrayOfContent,false);

        $this->handlePicture ($request,$profile);
        $this->handleRelations ($profile);
        $this->em->persist ($profile);
        $this->em->flush ();
        return $this->serializer->serializeOne ($profile,$acceptFormat,['groups'=>self::OWNER_GROUPS]);
    }

    /**
     * @throws CustomException
     */
    public function read(Request $request, $id, $context = [])
    {
        $acceptFormat=$this->typeMapper->resolveAcceptWith($request);
        /**
         * @var User $user
         */
        $user= $this->loadUser ($id);
        if ($user && $user instanceof User){
            $groups=$this->ownerService->isOwner ($user) ||$this->ownerService->isAdmin ($user)?self::OWNER_GROUPS:self::GUEST_GROUPS;
            $data=$this->serializer->serializeOne ($user,$acceptFormat,['groups'=>$groups]);
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
        $acceptFormat=$this->typeMapper->resolveAcceptWith  ($request);
        $users=$this->em->getRepository (User::class)->findAll ();
        return $this->serializer->serializeList ($users,$acceptFormat,['groups'=>'short_user']);

    }

    public function delete($id, $context = [])
    {
        $this->isAllowed ($id,'delete');
        $this->em->remove ($this->loadUser ($id));
        $this->em->flush ();

    }


    /**
     * cas content-type est multipart avec un champ data et picture
     * @param array $data
     * @return User
     */
    protected function newWithFormSubmit(array $data){
        $profile=new User();
        $form=$this->formFactory->create (UserType::class,$profile, ['csrf_protection' => false]);
        $form->submit($data,false);
        /**
         * @var User $profile
         */

        $profile=$form->getData ();

        if(!$form->isValid ()){
            $errors = $this->validator->validate($profile);
            if (count($errors) > 0) {
                $errorsString="";
                foreach ($errors as $error){
                    $errorsString.= (string) $error;
                }
                throw new CustomException("Invalid data ".str_replace ('Object(App\\Entity\\User)','',$errorsString));
            }
             /**
             $errors=$form->getErrors (true);
             $current=$errors->current ();
             $param=$current->getMessageParameters ();
             $message=$current->getMessage ();
             $message=sprintf ($message." %s ",implode (',',$param));
             throw new CustomException($message);
            **/
        }
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
            if(empty($skill->getLevel ())){
                $lDescrition=$skill->getLevelDescription ();
                if($lDescrition){
                    /**
                     * @var Level $level
                     */
                    $level=$this->em->getRepository (Level::class)->findOneBy (["description"=>$lDescrition]);
                    if(!$level){
                        throw new CustomException(" Unknow Field whic description is $lDescrition");
                    }
                    $skill->setLevel ($level);
                }else{
                    throw new CustomException("You provide a skills without  no level, provide a level for by Skill");
                }

            }

                $fDescriptions=$skill->getFieldsDescription ();
               if (count ($fDescriptions)) {
                   /**
                    * @var Field $field
                    */
                   foreach ($fDescriptions as $description) {
                       $field = $this -> em -> getRepository ( Field::class ) -> findOneBy ( ["description" => $description] );
                       if(!$field){
                           throw new CustomException(" Unknow Field whic description is $description");
                       }
                       $skill -> addField ( $field );
                   }
               }else{
                   throw new CustomException("You provide a skills without  no field, provide a least one Field by Skill");
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

    /**
     * @param $userId
     * @param string $action
     * @return bool
     * @throws CustomException
     */
     public function isAllowed($userId,$action='edit or delete'){
         $user= $this->loadUser ($userId);

         if( $this->ownerService->isOwner ($user) || $this->ownerService->isAdmin ($user)){
             return true ;
         }else{
             throw new \Exception("You are not allowed to $action this profil");
         }

     }

}
/**
 * AbstractNormalizer::OBJECT_TO_POPULATE n'est utilisé que pour l'objet de niveau supérieur.
 * Si cet objet est la racine d'une arborescence, tous les éléments enfants qui existent dans les données normalisées seront recréés avec de nouvelles instances.
 * Lorsque l'option AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE est définie sur true, les enfants existants de la racine OBJECT_TO_POPULATE sont mis à jour à partir des données normalisées, au lieu que le dénormaliseur les recrée.
 * Notez que DEEP_OBJECT_TO_POPULATE ne fonctionne que pour les objets enfants uniques, mais pas pour les tableaux d'objets. Ceux-ci seront toujours remplacés lorsqu'ils sont présents dans les données normalisées.
 *  // $profile = $this->serializer ->deserialize (trim ($data),User::class,$contentFormat,[AbstractNormalizer::OBJECT_TO_POPULATE => $profile,AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true]);
 */
