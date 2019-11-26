<?php

namespace AppBundle\Form;

use AppBundle\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NewsType
 * @package AppBundle\Form
 */
class NewsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleFr', TextType::class, ['label' => 'Title FR'])
            ->add('titleEn', TextType::class, ['label' => 'Title EN'])
            ->add('contentFr', TextareaType::class, ['label' => 'Content FR', 'required' => false])
            ->add('contentEn', TextareaType::class, ['label' => 'Content EN', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => News::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'news';
    }


}
