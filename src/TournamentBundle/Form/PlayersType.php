<?php

namespace TournamentBundle\Form;

use AppBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Entity\User;

/**
 * Class PlayersType
 * @package TournamentBundle\Form
 */
class PlayersType extends AbstractType
{
    /** @var User[] */
    private $users;

    /** @var array */
    private $tags;

    /** @var Tag */
    private $tag;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->users = $options['data']['users'];
        $this->tags = $options['data']['tags'];
        $this->tag = $options['data']['tag'];
        $game = $this->tag->getGame();
        $methodName = 'getName'.ucfirst($options['data']['locale']);

        /** @var User $user */
        foreach ($this->users as $user) {
            $builder
                ->add('user_'.$user->getId(), CheckboxType::class, ['label' => $user->getUsername(), 'required' => false])
                ->add('tag_'.$user->getId(), TextType::class, [
                    'label' => $this->tag->$methodName().' ('.$game->getName().')',
                    'required' => false,
                    'data' => $this->tags[$user->getId()] ?? null
                ])
            ;
        }
    }
}
