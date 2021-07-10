<?php

namespace App\Form;

use App\Entity\Training;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institution')
            ->add('city')
            ->add('country',CountryType::class)
            ->add('adresse')
            ->add('qualification')
            ->add('title')
            ->add('startedDate',DateType::class,['widget' => 'single_text'])
            ->add('endDate',DateType::class,['widget' => 'single_text'])
            ->add('description')
           // ->add('createdAt',DateTimeType::class,['widget' => 'single_text']) vu que c'est pas du DateTimeImmutable, il m'envoie balader
            ->add('updatedAt',DateTimeType::class,['widget' => 'single_text'])
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Training::class,
        ]);
    }
}
