<?php

namespace TournamentBundle\Form;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use TournamentBundle\Entity\Lan;

/**
 * Class LanRegistrationType
 * @package TournamentBundle\Form
 */
class LanRegistrationType extends AbstractType
{
    /** @var string */
    private $tag;

    /** @var Lan */
    private $lan;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->tag = $options['data']['tag'];
        $this->lan = $options['data']['lan'];

        $gameName = $this->lan->getGame()->getName();

        $label = $this->tag.' ('.$gameName.')';
        if ($this->lan->isTeam()) {
            $label = 'Nom de votre équipe';
        }

        $builder->add('name', TextType::class, ['label' => $label]);

        if ($this->lan->isTeam()) {
            for ($i = 0; $i < $this->lan->getFormat(); $i++) {
                $builder->add('username_'.$i, TextType::class, [
                    'label' => $this->tag.' #'.($i + 1).' ('.$gameName.')'
                ]);
            }
        }

        $builder
            ->add('email', EmailType::class)
            ->add('captcha', EWZRecaptchaType::class, ['mapped' => false, 'constraints' => new RecaptchaTrue()])
        ;
    }
}
