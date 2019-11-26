<?php

namespace AdminBundle\Form;

use TournamentBundle\Entity\RankingLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RankingLevelType
 * @package AdminBundle\Form
 */
class RankingLevelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameFr')
            ->add('nameEn')
            ->add('eloMax')
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('file', FileType::class, ['label' => 'Logo', 'required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RankingLevel::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_rankinglevel';
    }


}
