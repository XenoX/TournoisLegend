<?php

namespace UserBundle\Form;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class RegistrationType
 * @package UserBundle\Form
 */
class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'form.register.username', 'attr' => [
                'placeholder' => 'form.register.username', 'autofocus' => true
            ]])
            ->add('email', EmailType::class, ['label' => 'form.register.email', 'attr' => [
                'placeholder' => 'form.register.email'
            ]])
            ->add('password', RepeatedType::class, [
                'error_bubbling' => null,
                'type' => PasswordType::class,
                'invalid_message' => 'password.invalid_message',
                'first_options'  => ['label' => 'form.register.password', 'attr' => [
                    'placeholder' => 'form.register.password', 'error_bubbling' => null
                ]],
                'second_options' => ['label' => 'form.register.password_repeat', 'attr' => [
                    'placeholder' => 'form.register.password_repeat', 'error_bubbling' => null
                ]]
            ])
            ->add('country', CountryType::class, ['label' => 'form.register.country', 'data' => 'FR',
                'preferred_choices' => ['FR', 'BE', 'DE', 'ES', 'GB', 'IT', 'PT', 'SE', 'TN', 'MA']
            ])
            ->add('gender', ChoiceType::class, ['choices' => [
                'gender.male' => 'm',
                'gender.female' => 'f',
                'gender.robot' => 'r'
            ]])
            ->add('captcha', EWZRecaptchaType::class, [
                'mapped' => false,
                'constraints' => new RecaptchaTrue()
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
