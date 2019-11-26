<?php

namespace TournamentBundle\Form;

use AppBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ConfirmType
 * @package TournamentBundle\Form
 */
class ConfirmType extends AbstractType
{
    /** @var Tag */
    private $tag;

    /** @var string */
    private $locale;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->tag = $options['data']['tag'];
        $this->locale = $options['data']['locale'];

        $name = 'fr' === $this->locale ? $this->tag->getNameFr() : $this->tag->getNameEn();

        $builder->add('value', TextType::class, ['label' => $name.' ('.$this->tag->getGame()->getName().')']);
    }
}
