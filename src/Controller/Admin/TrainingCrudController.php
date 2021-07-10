<?php

namespace App\Controller\Admin;

use App\Entity\Training;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TrainingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Training::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm (),
            TextField::new('Institution'),
            TextField::new('City'),
            CountryField::new('Country'),
            TextField::new('Adresse'),
            TextField::new('Qualification')->setLabel ('Graduation'),
            TextField::new('Title'),
            DateTimeField::new('StartedDate'),
            DateTimeField::new('EndDate'),
            AssociationField::new ('user'),
            TextareaField::new('description'),
            DateTimeField::new('createdAt')->onlyOnDetail (),
            DateTimeField::new('updatedAt')->onlyOnDetail (),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize (6)
            ;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add (Crud::PAGE_INDEX,Action::DETAIL)
            ->remove (crud::PAGE_INDEX,Action::EDIT)
            ;
    }
}
