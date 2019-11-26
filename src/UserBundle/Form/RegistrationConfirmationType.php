<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class RegistrationConfirmationType
 * @package UserBundle\Form
 */
class RegistrationConfirmationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('birthdate', BirthdayType::class, [
                'label' => 'form.register_confirmation.birthdate',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('website', UrlType::class, ['label' => 'form.register_confirmation.website', 'required' => false])
            ->add('about', TextareaType::class, ['label' => 'form.register_confirmation.about', 'required' => false,
                'attr' => ['maxlength' => 200, 'rows' => 5]
            ])
            ->add('discord', TextType::class, ['required' => false,
                'attr' => ['placeholder' => 'abcde#1234']
            ])
            ->add('facebook', UrlType::class, ['required' => false])
            ->add('twitter', UrlType::class, ['required' => false])
            ->add('twitch', UrlType::class, ['required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
