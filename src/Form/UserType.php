<?php

namespace App\Form;

use App\Entity\Experience;
use App\Entity\SearchedSkill;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apiToken')
            ->add('userName')
           // ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')->setRequired (false)
            ->add('birthDate',DateTimeType::class,['widget' => 'single_text'])
            ->add('roles')
            ->add('city')
            ->add('adresse')
            ->add('gender')
            ->add ('picture',VichFileType::class)
            ->add ('filename')
            ->add ('password')
            ->add('picturesPath')
            ->add('profileDescription')
            ->add ('trainings',CollectionType::class,[
                'entry_type' => TrainingType::class,'allow_add' => true])
            ->add ('experiences',CollectionType::class,[
                'entry_type' => ExperienceType::class,'allow_add' => true])
            ->add ('ownSkills',CollectionType::class,[
                'entry_type' => OwnSkillType::class,'allow_add' => true])
            ->add ('searchedSkills',CollectionType::class,[
                'entry_type' => SearchedSkillType::class,'allow_add' => true])
         //   ->add('createdAt',DateTimeType::class,['widget' => 'single_text'])->setRequired (false)
            ->add('updatedAt',DateTimeType::class,['widget' => 'single_text'])

        ;

        /**
         * https://symfony.com/doc/current/reference/forms/types/collection.html
         * https://symfony.com/doc/current/reference/forms/types/form.html
         */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
/**
private $trainings;
private $experiences;
private $ownSkills;
private $searchedSkills;
 */
