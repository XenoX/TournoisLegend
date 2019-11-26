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
 * Class TeamManageType
 * @package UserBundle\Form
 */
class TeamManageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, ['label' => 'form.team_manage.logo', 'required' => false])
            ->add('fileBanner', FileType::class, ['label' => 'form.team_manage.banner', 'required' => false])
            ->add('tag', TextType::class, ['label' => 'form.team_manage.tag', 'attr' => ['maxlength' => 6]])
            ->add('name', TextType::class, ['label' => 'form.team_manage.name', 'attr' => ['maxlength' => 30]])
            ->add('website', UrlType::class, ['label' => 'form.team_manage.website', 'required' => false])
            ->add('facebook', UrlType::class, ['required' => false])
            ->add('twitter', UrlType::class, ['required' => false])
            ->add('twitch', UrlType::class, ['required' => false])
            ->add('description', TextareaType::class, ['label' => 'form.team_manage.description', 'required' => false])
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
