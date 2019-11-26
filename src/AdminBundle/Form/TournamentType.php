<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Mode;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TournamentBundle\Entity\Description;
use TournamentBundle\Entity\Organizer;
use TournamentBundle\Entity\Reward;
use TournamentBundle\Entity\Rules;
use TournamentBundle\Entity\Tournament;

/**
 * Class TournamentType
 * @package AdminBundle\Form
 */
class TournamentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('size', ChoiceType::class, ['label' => 'Size', 'choices' => $this->getSizeChoices(), 'preferred_choices' => [32, 64]])
            ->add('format', ChoiceType::class, ['label' => 'Format', 'choices' => $this->getFormatChoices(), 'preferred_choices' => [5, 3]])
            ->add('rankingRatio', NumberType::class, ['label' => 'Ranking Ratio'])
            ->add('mapNameFr', TextType::class, ['label' => 'Map FR', 'required' => false])
            ->add('mapNameEn', TextType::class, ['label' => 'Map EN', 'required' => false])
            ->add('riotId', IntegerType::class, ['label' => 'Riot ID', 'required' => false])
            ->add('registrationStartAt', DateTimeType::class, ['label' => 'Registration start at',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('registrationStopAt', DateTimeType::class, ['label' => 'Registration stop at',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('startAt', DateTimeType::class, ['label' => 'Start At',
                'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy HH:mm'
            ])
            ->add('hiddenParticipant', CheckboxType::class, ['label' => 'Hidden participants', 'required' => false])
            ->add('startAuto', CheckboxType::class, ['label' => 'Auto start', 'required' => false])
            ->add('activated', CheckboxType::class, ['label' => 'Activated', 'required' => false])
            ->add('mode', EntityType::class, [
                'label' => 'Mode',
                'class' => Mode::class,
                'choice_label' => 'fullName'
            ])
            ->add('description', EntityType::class, [
                'label' => 'Description',
                'class' => Description::class,
                'choice_label' => 'fullName'
            ])
            ->add('reward', EntityType::class, [
                'label' => 'Reward',
                'class' => Reward::class,
                'choice_label' => 'fullName',
                'required' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('r')
                        ->where('r.lan = :lan')
                        ->setParameter('lan', false)
                    ;
            }])
            ->add('organizer', EntityType::class, [
                'label' => 'Organizer',
                'class' => Organizer::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('rules', EntityType::class, [
                'label' => 'Rules',
                'class' => Rules::class,
                'choice_label' => 'name',
                'required' => false
            ])
        ;
    }

    /**
     * @return array
     */
    private function getSizeChoices()
    {
        $choices = [];
        for ($i = 2; $i <= 512; $i = $i * 2) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    /**
     * @return array
     */
    private function getFormatChoices()
    {
        $choices = [];
        for ($i = 1; $i <= 10; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class
        ]);
    }
}
