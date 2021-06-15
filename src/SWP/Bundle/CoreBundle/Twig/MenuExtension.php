<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\MenuExtension as KnpMenuExtension;

class MenuExtension extends KnpMenuExtension
{
    /**
     * {@inheritdoc}
     */
    public function get($menu, array $path = [], array $options = []): ItemInterface
    {
        try {
            return parent::get($menu, $path, $options);
        } catch (\InvalidArgumentException $e) {
            // allow to render void
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render($menu, array $options = [], $renderer = null): string
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
    public function getBreadcrumbsArray($menu, $subItem = null): array
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
    public function getCurrentItem($menu): ItemInterface
    {
        try {
            return parent::getCurrentItem($menu);
        } catch (\InvalidArgumentException $e) {
            // allow to render empty value
        }
    }
}
