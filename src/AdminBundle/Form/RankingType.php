<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Mode;
use TournamentBundle\Entity\Ranking;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RankingType
 * @package AdminBundle\Form
 */
class RankingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameFr')
            ->add('nameEn')
            ->add('startAt', DateTimeType::class, ['label' => 'Start at',
               'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm', 'required' => false
            ])
            ->add('stopAt', DateTimeType::class, ['label' => 'Stop at',
               'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm', 'required' => false
            ])
            ->add('baseElo')
            ->add('seasonNumber')
            ->add('mode', EntityType::class, [
                'label' => 'Mode',
                'class' => Mode::class,
                'choice_label' => 'fullName',
                'required' => false
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Ranking::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_ranking';
    }


}
