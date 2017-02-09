<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Twig;

use Knp\Menu\Twig\MenuExtension as KnpMenuExtension;

class MenuExtension extends KnpMenuExtension
{
    /**
     * {@inheritdoc}
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        $result = '';

        try {
            $result = parent::get($menu, $path, $options);
        } catch (\InvalidArgumentException $e) {
            // when menu not found
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function render($menu, array $options = array(), $renderer = null)
    {
        try {
            return parent::render($menu, $options, $renderer);
        } catch (\InvalidArgumentException $e) {
            // allow to render empty value
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBreadcrumbsArray($menu, $subItem = null)
    {
        try {
            return parent::getBreadcrumbsArray($menu, $subItem);
        } catch (\InvalidArgumentException $e) {
            // allow to render empty value
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentItem($menu)
    {
        try {
            return parent::getCurrentItem($menu);
        } catch (\InvalidArgumentException $e) {
            // allow to render empty value
        }
    }
}
