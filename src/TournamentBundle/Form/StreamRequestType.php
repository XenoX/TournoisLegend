<?php

namespace TournamentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class StreamRequestType
 * @package TournamentBundle\Form
 */
class StreamRequestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('channel', TextType::class, [
            'label' => 'form.stream_request.channel', 'attr' => ['value' => $options['data']['lastChannelName']]
        ]);
    }
}
