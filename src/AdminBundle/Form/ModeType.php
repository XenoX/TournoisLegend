<?php

namespace AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Game;
use AppBundle\Entity\Mode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ModeType
 * @package AdminBundle\Form
 */
class ModeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileBanner', FileType::class, ['label' => 'Banner', 'required' => false])
            ->add('nameFr', TextType::class, ['label' => 'Name FR'])
            ->add('nameEn', TextType::class, ['label' => 'Name EN'])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('game', EntityType::class, [
                'label' => 'Game',
                'class' => Game::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->where('g.activated = :activated')
                        ->setParameter('activated', true)
                    ;
                }
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mode::class
        ]);
    }
}
