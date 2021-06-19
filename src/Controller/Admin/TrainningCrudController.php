<?php

namespace App\Controller\Admin;

use App\Entity\Trainning;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TrainningCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trainning::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm ()->hideOnIndex (),
            TextField::new('Institution'),
            TextField::new('City'),
            TextField::new('Country'),
            TextField::new('Adresse'),
            TextField::new('Qualification')->setLabel ('Graduation'),
            TextField::new('Title'),
            DateTimeField::new('StartedDate'),
            DateTimeField::new('EndDate'),
            TextareaField::new('description'),
            DateTimeField::new('createdAt')->onlyWhenCreating (),
            DateTimeField::new('updatedAt'),
        ];
    }
}
