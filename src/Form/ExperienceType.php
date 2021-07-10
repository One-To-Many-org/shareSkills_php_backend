<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institution')
            ->add('city')
            ->add('country')
            ->add('adresse')
            ->add('qualification')
            ->add('title')
            ->add('startedDate',DateTimeType::class,['widget' => 'single_text'])
            ->add('endDate',DateTimeType::class,['widget' => 'single_text'])
            ->add('description')
           // ->add('createdAt',DateTimeType::class,['widget' => 'single_text'])
            ->add('updatedAt',DateTimeType::class,['widget' => 'single_text'])
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
