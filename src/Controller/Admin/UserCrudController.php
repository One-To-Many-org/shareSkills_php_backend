<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Skills;
use App\Entity\User;
use App\Repository\CountryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
        // "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjQ0OTE3NTYsImV4cCI6MTYyNDQ5NTM1Niwicm9sZXMiOltdLCJ1c2VybmFtZSI6IkFubmFiZWwgVWxscmljaC02MyJ9.YiJ7NEoq7lrbOlKaXbH5_ucT2ZYwtjXaX1s4_6NC9vpRl8ZqASvqaC_pBXcc60unsqSaOOCz9gFSi00_Nh3U7WsxHNZdyFNbz-NaMkUk7vihrHMSqjAmYJI1UzDcTxRRdio_GU0u77W9TiCugx6wdrnEJSz28IwS8xqKE_4asUoeXdL9L6ET2RZBhnXGr95vDw1jLoLavI-VWK1-wP8OByC2QPt2Hj7q09OPjMyVvQJgkeEVmkN5nnN_bODuM0VIQQX4upakjEfAcrO2_1EH7MCS0NmBFnoFzg34Aq6aH6nXXGXp03Lv-8YqsL8KG6nFk7C-A0nXjG86qlxKlcRFTQ"
        return [
            IdField::new('id')->hideOnForm ()->hideOnIndex (),
            TextField::new ('picture')->setFormType (VichFileType::class)->setTemplatePath ('admin/picture.html.twig')->setCustomOption ('base_path','profiles/pictures')->hideOnIndex (),
            TextField::new ('fileName')->hideOnForm ()->hideOnDetail ()->hideOnIndex (),
            TextField::new('Email')->setRequired (true),
            TextField::new('Password')->setRequired (true)->onlyWhenCreating (),
            TextField::new('FirstName')->setRequired (true),
            TextField::new('LastName')->setRequired (true),
            TextField::new ('apiToken')->hideOnIndex ()->hideOnDetail ()->hideOnForm (),
            DateField::new('BirthDate')->setRequired (true),
            TextField::new('UserName'),
            ArrayField::new('Roles'),
            TextField::new('Phone'),
            ChoiceField::new('City')->setChoices ($this->getCities ()),
            TextField::new('Country')->onlyOnDetail (),
            TextField::new('Adresse'),
            ChoiceField::new('Gender')->setChoices (['Mr'=>'Mr','Mme'=>'Mme','other'=>'other'])->setRequired (true),
            TextField::new('PicturesPath')->hideOnIndex ()->hideOnForm (),
            TextareaField::new('ProfileDescription'),
            DateTimeField::new('createdAt')->hideOnForm ()->hideOnIndex (),
            DateTimeField::new('updatedAt')->hideOnForm ()->hideOnIndex (),
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
