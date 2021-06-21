<?php

namespace App\Controller\Admin;

use App\Entity\SearchedSkill;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class SearchedSkillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SearchedSkill::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm (),
            // TextField::new('status'),
            AssociationField::new('field')->setFormTypeOption ('required',true),
            AssociationField::new('level'),
            TextareaField::new('description'),
            AssociationField::new ('user'),
            DateTimeField::new('createdAt')->onlyWhenCreating (),
            DateTimeField::new('updatedAt'),
        ];
    }
}
