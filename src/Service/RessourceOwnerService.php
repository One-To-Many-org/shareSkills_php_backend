<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RessourceOwnerService
{
    /**
     * @var TokenStorageInterface
     */
    private  $ts;
    private  $aci;

    public function __construct(TokenStorageInterface $tokenStorage,AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->ts=$tokenStorage;
        $this->aci=$authorizationChecker;
    }

    /**
     * On est propriétaire d'une ressource si on est l'utilisateur référencé par la ressouce est celui qui est connecté
     * Si personne n'est authentifié  c'est qu'on ne sais même pas qui demande la ressouce dont n'est pas propriétaire
     * @param User $user
     * @return false
     */
    public function isOwner(User $user){
        $token=$this->ts->getToken ();
        /**
         * s'il y a un utilisateur avec token
         */
        if($token){
            return $token->getUser ()->isSame ($user);
        }
       return false;
    }

    public function isAdmin(User $user){
        return $this->aci->isGranted ('ROLE_ADMIN',$user);
    }

    public function canAccessToFullProfile(User $user){
        return $this->isAdmin ($user) && $this->isOwner ($user);
    }
}
