<?php

namespace App\DataFixtures;

use App\Entity\Field;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FieldFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $fields=['Mathématiques','Physiques','Biology','English','Agronomy','Economy'];

        foreach ($fields as $fieldName){
            $field = new Field();
            $field->setDescription ($fieldName);
            $field->setCreatedAt (new \DateTime());
            $field->setUpdatedAt  (new \DateTime());
            $manager->persist($field);
        }

        $manager->flush();
    }
}
