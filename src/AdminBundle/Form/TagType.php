<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Game;
use AppBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType
 * @package AdminBundle\Form
 */
class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameFr', TextType::class, ['label' => 'Name FR'])
            ->add('nameEn', TextType::class, ['label' => 'Name EN'])
            ->add('game', EntityType::class, [
                'label' => 'Game',
                'class' => Game::class,
                'choice_label' => 'fullName'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class
        ]);
    }
}
