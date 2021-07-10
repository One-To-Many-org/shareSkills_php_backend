<?php


namespace App\Serializer;


use App\Entity\User;
use App\Exceptions\CustomException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileSerializer
{
    public $serializer;
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer=$serializer;
       // parent ::__construct ( [new XmlEncoder(),new JsonEncoder(), new YamlEncoder(),new CsvEncoder()], [ ] );
        //extends \Symfony\Component\Serializer\Serializer;
    }

    public function serializeOne(User $user, $type, array $context=[]){
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

    /**
     * @param $users
     * @param $type
     * @param array $context
     * @return string
     */
    public function serializeList($users, $type, array $context=[]){

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



}
