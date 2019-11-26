<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GameType
 * @package AdminBundle\Form
 */
class GameType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['label' => 'Logo', 'required' => false])
            ->add('fileBanner', FileType::class, ['label' => 'Banner', 'required' => false])
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('shortName', TextType::class, ['label' => 'Short name'])
            ->add('solo', CheckboxType::class, ['label' => 'Solo', 'required' => false])
            ->add('team', CheckboxType::class, ['label' => 'Team', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class
        ]);
    }
}
