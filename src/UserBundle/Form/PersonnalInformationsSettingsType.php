<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class PersonnalInformationsSettingsType
 * @package UserBundle\Form
 */
class PersonnalInformationsSettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'form.settings.username', 'attr' => ['autofocus' => true]])
            ->add('email', EmailType::class, ['label' => 'form.settings.email'])
            ->add('birthdate', BirthdayType::class, ['label' => 'form.settings.birthdate'])
            ->add('country', CountryType::class, ['label' => 'form.settings.country',
                'preferred_choices' => ['FR', 'BE', 'DE', 'ES', 'GB', 'IT', 'PT', 'SE', 'TN', 'MA']
            ])
            ->add('gender', ChoiceType::class, ['label' => 'form.settings.gender', 'choices' =>
                [
                    'gender.male' => 'm',
                    'gender.female' => 'f',
                    'gender.robot' => 'r'
                ]
            ])
            ->add('about', TextareaType::class, ['label' => 'form.settings.about', 'required' => false,
                'attr' => ['maxlength' => 200, 'rows' => 5]
            ])
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
