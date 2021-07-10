<?php

namespace App\Form;

use App\Entity\Field;
use App\Entity\OwnSkill;
use App\Repository\FieldRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnSkillType extends AbstractType
{
    public function __construct(FieldRepository $rep){

        /**
         *  $this->transformer=new CollectionToArrayTransformer() ;
        $fields=$rep->findAll ();
        foreach ($fields as $field){
        $this->fields[$field->getDescription ()]=$field;
        }
        $this->fields=array_map (function (Field $field){
            return $field->getDescription ();
        },$rep->findAll ());
     **/
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('createdAt',DateTimeType::class,['widget' => 'single_text'])->setRequired (false)
            ->add('updatedAt',DateTimeType::class,['widget' => 'single_text'])
            ->add('title')
            ->add ('fieldsDescription',CollectionType::class,['entry_type'=>TextType::class,'allow_add' => true])
            ->add('fields',CollectionType::class,['entry_type' => FieldType::class,'allow_add' => true])
            ->add('level')
            ->add ('levelDescription')
            ->add('user')
        ;
        /**
         * ->add('fields',EntityType::class, [
        'class' => Field::class,
        'query_builder' => function (FieldRepository $er) {
        return $er->createQueryBuilder ('f')->orderBy('f.description', 'ASC');
        },
        // 'choice_label' => 'description',
        ])->addModelTransformer ($this->transformer)
         */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OwnSkill::class,
        ]);
    }

}
