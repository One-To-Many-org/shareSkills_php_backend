<?php

namespace App\Controller\Admin;

use App\Entity\OwnSkill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OwnSkillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OwnSkill::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField ::new ( 'id' ) -> hideOnForm (),
            TextField::new('title'),
            AssociationField ::new ( 'fields' ) -> setFormTypeOption ( 'required', false )->setTemplatePath ('admin/field.html.twig')->hideOnIndex (),
            AssociationField ::new ( 'level' ),
            TextareaField ::new ( 'description' ),
            AssociationField ::new ( 'user' ),
            DateTimeField ::new ( 'createdAt' ) ->onlyOnDetail (),
            DateTimeField ::new ( 'updatedAt' )->onlyOnDetail (),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize (8)
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
