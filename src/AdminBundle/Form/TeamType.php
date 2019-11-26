<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\Team;

/**
 * Class TeamType
 * @package AdminBundle\Form
 */
class TeamType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['label' => 'Avatar', 'required' => false])
            ->add('fileBanner', FileType::class, ['label' => 'Banner', 'required' => false])
            ->add('tag', TextType::class, ['label' => 'Tag'])
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('description', TextareaType::class, ['label' => 'About', 'required' => false,
                'attr' => ['maxlength' => 200, 'rows' => 5]
            ])
            ->add('website', UrlType::class, ['label' => 'Website', 'required' => false])
            ->add('twitch', TextType::class, ['label' => 'Twitch', 'required' => false])
            ->add('facebook', TextType::class, ['label' => 'Facebook', 'required' => false])
            ->add('twitter', TextType::class, ['label' => 'Twitter', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('deleted', CheckboxType::class, ['label' => 'Deleted', 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class
        ]);
    }
}
