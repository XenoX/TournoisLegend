<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PasswordSettingsType
 * @package UserBundle\Form
 */
class PasswordSettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, ['label' => 'form.settings.password'])
            ->add('new_password', RepeatedType::class, [
                'error_bubbling' => null,
                'type' => PasswordType::class,
                'invalid_message' => 'password.invalid_message',
                'first_options'  => ['label' => 'form.settings.new_password', 'attr' => ['error_bubbling' => null]],
                'second_options' => ['label' => 'form.settings.new_password_repeat', 'attr' => ['error_bubbling' => null]]
            ])
        ;
    }
}
