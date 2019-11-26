<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PasswordForgotType
 * @package UserBundle\Form
 */
class PasswordForgotType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'error_bubbling' => null,
                'type' => PasswordType::class,
                'invalid_message' => 'password.invalid_message',
                'first_options'  => ['label' => 'form.forgot_password.new_password', 'attr' => [
                    'error_bubbling' => null
                ]],
                'second_options' => ['label' => 'form.forgot_password.new_password_repeat', 'attr' => [
                    'error_bubbling' => null
                ]]
            ])
        ;
    }
}
