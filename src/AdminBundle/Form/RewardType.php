<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Reward;

/**
 * Class RewardType
 * @package AdminBundle\Form
 */
class RewardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('firstFr', TextType::class, ['label' => 'First FR'])
            ->add('firstEn', TextType::class, ['label' => 'First EN'])
            ->add('secondFr', TextType::class, ['label' => 'Second FR', 'required' => false])
            ->add('secondEn', TextType::class, ['label' => 'Second EN', 'required' => false])
            ->add('thirdFr', TextType::class, ['label' => 'Third FR', 'required' => false])
            ->add('thirdEn', TextType::class, ['label' => 'Third EN', 'required' => false])
            ->add('fourthFr', TextType::class, ['label' => 'Fourth FR', 'required' => false])
            ->add('fourthEn', TextType::class, ['label' => 'Fourth EN', 'required' => false])
            ->add('fifthToEighthFr', TextType::class, ['label' => 'Fifth to eighth FR', 'required' => false])
            ->add('fifthToEighthEn', TextType::class, ['label' => 'Fifth to eighth EN', 'required' => false])
            ->add('lan', CheckboxType::class, ['label' => 'Lan', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Reward::class]);
    }
}
