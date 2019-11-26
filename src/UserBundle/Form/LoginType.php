<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginType
 * @package UserBundle\Form
 */
class LoginType extends AbstractType
{
    /** @var string|null */
    protected $lastUsername;

    /**
     * LoginType constructor.
     *
     * @param null $lastUsername
     */
    public function __construct($lastUsername = null)
    {
        $this->lastUsername = $lastUsername;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, ['label' => 'form.login.username', 'attr' => [
                'placeholder' => 'form.login.username', 'value' => $this->lastUsername, 'autofocus' => true
            ]])
            ->add('_password', PasswordType::class, ['label' => 'form.login.password', 'attr' => [
                'placeholder' => 'form.login.password', 'name' => '_password'
            ]])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'form.login.remember_me', 'required' => false
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
