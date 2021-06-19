<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExperienceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Experience::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm ()->hideOnIndex (),
            TextField::new('Institution')->setLabel ('Company'),
            TextField::new('City'),
            TextField::new('Country'),
            TextField::new('Adresse'),
            TextField::new('Qualification')->setLabel ('Poste'),
            TextField::new('Title'),
            DateTimeField::new('StartedDate'),
            DateTimeField::new('EndDate'),
            TextareaField::new('description'),
            DateTimeField::new('createdAt')->onlyWhenCreating (),
            DateTimeField::new('updatedAt'),
        ];
    }


    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
