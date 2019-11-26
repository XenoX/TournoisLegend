<?php

namespace AppBundle\Twig;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;

/**
 * Class AvatarExtension
 * @package AppBundle\Twig
 */
class AvatarExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('avatar', [$this, 'getAvatar'])];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getAvatar(string $name)
    {
        return base64_encode(
            (new InitialAvatar())
            ->name($name)
            ->size(200)
            ->autoFont()
            ->length(str_word_count($name) > 2 ? 2 : str_word_count($name))
            ->background('#f0f0f0')
            ->color('#cccccc')
            ->generate()
            ->stream('jpeg', 90)
            ->getContents()
        );
    }
}
