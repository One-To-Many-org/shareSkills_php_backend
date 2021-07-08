<?php


namespace App\Serializer;


use App\Entity\User;
use App\Exceptions\CustomException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class ProfileSerializer extends \Symfony\Component\Serializer\Serializer
{
    public function serializeOne(User $user, $type, array $context=[]){
        switch ($type) {
            case 'xml':
                $defaultContext=[xmlEncoder::ROOT_NODE_NAME=>'profile'];
                $result=$this->serialize ($user,'xml',array_merge($defaultContext,$context));
                break;
            case 'json':
                $result=$this->serialize ($user,'json',$context);
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
                $result=$this->serialize (['numfound'=>count ($profiles),'profile-list'=>['profile'=>$profiles]],'xml',array_merge($defaultContext,$context));
                break;
            case 'json':
                $result=$this->serialize (['numfound'=>count ($users),'profile-list'=>$users],'json',$context);
                break;
            default:
                throw new CustomException("Unrecognized Format ".$type."for profile list Serialisation");
        }

        return $result;
    }



}
