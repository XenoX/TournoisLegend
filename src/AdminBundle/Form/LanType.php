<?php

namespace AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Description;
use TournamentBundle\Entity\Lan;
use TournamentBundle\Entity\Mail;
use TournamentBundle\Entity\Reward;
use TournamentBundle\Entity\Rules;

/**
 * Class LanType
 * @package AdminBundle\Form
 */
class LanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('slug', TextType::class, ['label' => 'Slug'])
            ->add('size', IntegerType::class, ['label' => 'Size'])
            ->add('bracket', TextareaType::class, ['label' => 'Bracket (iframe)', 'required' => false])
            ->add('bracketAmateur', TextareaType::class, ['label' => 'Bracket amateur (iframe)', 'required' => false])
            ->add('format', IntegerType::class, ['label' => 'Format (1, 3, 5)'])
            ->add('plan', TextareaType::class, ['label' => 'Plan Google Map'])
            ->add('price', IntegerType::class, ['label' => 'Price'])
            ->add('twitch', TextType::class, ['label' => 'Twitch channel (name)', 'required' => false])
            ->add('dailymotion', TextType::class, ['label' => 'Dailymotion (iframe)', 'required' => false])
            ->add('registrationStartAt', DateTimeType::class, ['label' => 'Registration start at',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('registrationStopAt', DateTimeType::class, ['label' => 'Registration stop at',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('startAt', DateTimeType::class, ['label' => 'Start At',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('description', EntityType::class, [
                'class' => Description::class,
                'choice_label' => 'name',
                'label' => 'Description'
            ])->add('rules', EntityType::class, [
                'class' => Rules::class,
                'choice_label' => 'name',
                'label' => 'Rules',
                'required' => false
            ])
            ->add('reward', EntityType::class, [
                'class' => Reward::class,
                'choice_label' => 'name',
                'label' => 'Rewards',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.lan = :lan')
                        ->setParameter('lan', true)
                    ;
                }
            ])
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'name',
                'label' => 'Game'
            ])
            ->add('mail', EntityType::class, [
                'class' => Mail::class,
                'choice_label' => 'name',
                'label' => 'Mail',
                'required' => false
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lan::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminbundle_lan';
    }
}
