<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
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

class ExperienceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Experience::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm (),
            TextField::new('Institution')->setLabel ('Company'),
            TextField::new('City'),
            CountryField::new('Country'),
            TextField::new('Adresse'),
            TextField::new('Qualification')->setLabel ('Poste'),
            TextField::new('Title')->hideOnIndex (),
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
