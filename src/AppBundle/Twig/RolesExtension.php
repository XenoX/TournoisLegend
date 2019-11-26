<?php

namespace AppBundle\Twig;

use Symfony\Component\Translation\Translator;
use UserBundle\Entity\User;

/**
 * Class RolesExtension
 * @package AppBundle\Twig
 */
class RolesExtension extends \Twig_Extension
{
    /** @var array */
    private $roleNames;

    /** @var Translator */
    private $translator;

    /**
     * RolesExtension constructor.
     *
     * @param array      $roleNames
     * @param Translator $translator
     */
    public function __construct($roleNames, Translator $translator)
    {
        $this->roleNames = $roleNames;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('rank', [$this, 'rank'])];
    }

    /**
     * @param array $roles
     *
     * @return string
     */
    public function rank(array $roles)
    {
        $finalRole = User::ROLE_USER;
        foreach ($roles as $role) {
            $finalRole = $role;
        }

        return $this->translator->trans($this->roleNames[$finalRole]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'roles_extension';
    }
}
