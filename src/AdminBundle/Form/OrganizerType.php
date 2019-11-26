<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Organizer;

/**
 * Class OrganizerType
 * @package AdminBundle\Form
 */
class OrganizerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('file', FileType::class, ['label' => 'Logo', 'required' => false])
            ->add('website', TextType::class, ['label' => 'Website', 'required' => false])
            ->add('descriptionFr', TextareaType::class, ['label' => 'Description FR', 'required' => false])
            ->add('descriptionEn', TextareaType::class, ['label' => 'Description EN', 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organizer::class
        ]);
    }
}
