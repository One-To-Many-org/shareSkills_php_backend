<?php

namespace App\Form;

use App\Entity\SearchedSkill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchedSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
           // ->add('createdAt')
            ->add('updatedAt')
            ->add ('fieldDescription')
            ->add('field')
            ->add ('levelDescription')
            ->add('level')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchedSkill::class,
        ]);
    }
}
