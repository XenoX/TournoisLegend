<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Description;

/**
 * Class DescriptionType
 * @package AdminBundle\Form
 */
class DescriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('contentFr', TextareaType::class, ['label' => 'Content FR', 'required' => false])
            ->add('contentEn', TextareaType::class, ['label' => 'Content EN', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class
        ]);
    }
}
