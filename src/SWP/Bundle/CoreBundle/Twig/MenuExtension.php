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
use Twig\TwigFunction;

class MenuExtension extends KnpMenuExtension {
  /**
   * {@inheritdoc}
   */
  public function getMy($menu, array $path = [], array $options = []): ?ItemInterface {
    try {
      return parent::get($menu, $path, $options);
    } catch (\InvalidArgumentException $e) {
      return null;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function render($menu, array $options = [], $renderer = null): string {
    try {
      return parent::render($menu, $options, $renderer);
    } catch (\InvalidArgumentException $e) {
      // allow to render empty value
      return "";
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getBreadcrumbsArray($menu, $subItem = null): array {
    try {
      return parent::getBreadcrumbsArray($menu, $subItem);
    } catch (\InvalidArgumentException $e) {
      // allow to render empty value
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentItemMy($menu): ?ItemInterface {
    try {
      return parent::getCurrentItem($menu);
    } catch (\InvalidArgumentException $e) {
      return null;
    }
  }

  public function getFunctions(): array {
    return [
        new TwigFunction('knp_menu_get', [$this, 'getMy']),
        new TwigFunction('knp_menu_render', [$this, 'render'], ['is_safe' => ['html']]),
        new TwigFunction('knp_menu_get_breadcrumbs_array', [$this, 'getBreadcrumbsArray']),
        new TwigFunction('knp_menu_get_current_item', [$this, 'getCurrentItemMy']),
    ];
  }
}
