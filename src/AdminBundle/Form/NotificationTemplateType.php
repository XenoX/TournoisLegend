<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\NotificationTemplate;

/**
 * Class NotificationTemplateType
 * @package AdminBundle\Form
 */
class NotificationTemplateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name (dot-notation)'])
            ->add('contentFr', TextType::class, ['label' => 'Content FR'])
            ->add('contentEn', TextType::class, ['label' => 'Content EN'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NotificationTemplate::class
        ]);
    }
}
