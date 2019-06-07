<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DeveloperType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=> 'name'])
            ->add('email', TextType::class, ['label'=> 'email'])
            ->add('lastName', TextType::class, ['label'=> 'lastName'])
            ->add('phone', TextType::class, ['label'=> 'phone'])
            ->add('birth_date', DateType::class, ['label'=> 'birth_date','widget' => 'single_text',
            'format' => 'yyyy-MM-dd',])
            ->add('type')
            ->add('enterprise')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Developer',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_developer';
    }


}
