<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class UserType
 * @package AdminBundle\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Username', 'attr' => ['autofocus' => true]])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('country', CountryType::class, ['label' => 'Country', 'data' => 'FR',
                'preferred_choices' => ['FR', 'BE', 'DE', 'ES', 'GB', 'IT', 'PT', 'SE', 'TN', 'MA']
            ])
            ->add('gender', ChoiceType::class, ['choices' => ['Male' => 'm', 'Female' => 'f', 'Robot' => 'r']])
            ->add('birthdate',
                BirthdayType::class,
                ['label' => 'Birthdate', 'widget' => 'single_text', 'required' => false]
            )
            ->add('about', TextareaType::class, ['label' => 'About', 'required' => false,
                'attr' => ['maxlength' => 200, 'rows' => 5]
            ])
            ->add('website', UrlType::class, ['label' => 'Website', 'required' => false])
            ->add('discord', TextType::class, ['label' => 'Discord', 'required' => false])
            ->add('facebook', TextType::class, ['label' => 'Facebook', 'required' => false])
            ->add('twitter', TextType::class, ['label' => 'Twitter', 'required' => false])
            ->add('twitch', TextType::class, ['label' => 'Twitch', 'required' => false])
            ->add('roles', ChoiceType::class,
                [
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'VIP' => 'ROLE_VIP',
                        'Streamer' => 'ROLE_STREAMER',
                        'Tournament' => 'ROLE_TOURNAMENT',
                        'Modérateur' => 'ROLE_MODO',
                        'Administrateur' => 'ROLE_ADMIN'
                    ],
                    'required' => true,
                    'multiple' => true
                ]
            )
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('locked', CheckboxType::class, ['label' => 'Banned', 'required' => false])
            ->add('deleted', CheckboxType::class, ['label' => 'Deleted', 'required' => false])
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
