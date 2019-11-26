<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\Team;

/**
 * Class TeamCreateType
 * @package UserBundle\Form
 */
class TeamCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['label' => 'form.team_create.logo', 'required' => false])
            ->add('fileBanner', FileType::class, ['label' => 'form.team_create.banner', 'required' => false])
            ->add('name', TextType::class, ['label' => 'form.team_create.name', 'attr' => ['maxlength' => 30]])
            ->add('tag', TextType::class, ['label' => 'form.team_create.tag', 'attr' => ['maxlength' => 6]])
            ->add('website', UrlType::class, ['label' => 'form.team_create.website', 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'form.team_create.description', 'required' => false])
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
