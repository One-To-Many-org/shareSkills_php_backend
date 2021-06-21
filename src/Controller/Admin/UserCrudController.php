<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Skills;
use App\Entity\User;
use App\Repository\CountryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm ()->hideOnIndex (),
            TextField::new('Email')->setRequired (true),
            TextField::new('Password')->setRequired (true)->hideOnDetail ()->hideOnIndex (),
            TextField::new('FirstName')->setRequired (true),
            TextField::new('LastName')->setRequired (true),
            DateField::new('BirthDate')->setRequired (true),
            TextField::new('UserName'),
            TextField::new('Phone'),
            ChoiceField::new('City')->setChoices ($this->getCities ()),
            ChoiceField::new('Country')->onlyOnDetail (),
            TextField::new('Adresse'),
            ChoiceField::new('Gender')->setChoices (['Mr'=>'Mr','Mme'=>'Mme','other'=>'other'])->setRequired (true),
            TextField::new('PicturesPath')->hideOnIndex (),
            TextareaField::new('ProfileDescription'),
            DateTimeField::new('createdAt')->onlyWhenCreating (),
            DateTimeField::new('updatedAt'),
        ];
    }


    public function getCities(){
        $cityRep=   $this->getDoctrine ()->getManager ()->getRepository (City::class);

        $cities=  $cityRep->findAll ();
        $result=[];
        foreach ($cities as $city){
            /**
             * @var City $city
             */
            $name=$city->getFullName ();
            $result[$name]=$name;
        }
        return $result ;
    }

}
