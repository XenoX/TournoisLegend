<?php

namespace AdminBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;

/**
 * Class NotificationType
 * @package AdminBundle\Form
 */
class NotificationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['label' => 'type', 'choices' => $this->getTypeChoices()])
            ->add('value', TextType::class, ['label' => 'Value (id)'])
            ->add('template', EntityType::class, [
                'label' => 'Template',
                'class' => NotificationTemplate::class,
                'choice_label' => 'name'
            ])
        ;
    }

    private function getTypeChoices()
    {
        $reflectionClass = new \ReflectionClass(Notification::class);
        $constants = $reflectionClass->getConstants();

        $choices = [];
        foreach ($constants as $name => $constant) {
            $choices[$constant] = $constant;
        }

        return $choices;
    }
}
