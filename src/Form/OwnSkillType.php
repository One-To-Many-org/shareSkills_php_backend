<?php

namespace App\Form;

use App\Entity\OwnSkill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
           // ->add('createdAt')
            ->add('updatedAt')
            ->add('field')
            ->add ('fieldDescription')
            ->add('level')
            ->add ('levelDescription')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OwnSkill::class,
        ]);
    }
}
