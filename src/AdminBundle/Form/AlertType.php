<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Alert;

/**
 * Class AlertType
 * @package AdminBundle\Form
 */
class AlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentFr', TextType::class, ['label' => 'Content Fr'])
            ->add('contentEn', TextType::class, ['label' => 'Content En'])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'info (light blue)' => 'info',
                    'warning (orange)' => 'warning',
                    'danger (red)' => 'danger',
                    'success (green)' => 'success',
                    'primary (blue)' => 'primary'
                ],
                'required' => true
            ])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alert::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminbundle_tournamentalert';
    }
}
