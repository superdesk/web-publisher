<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle\Extension;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;

class ExtensionChain implements ExtensionInterface
{
    /**
     * @var \SplPriorityQueue|ExtensionInterface[]
     */
    private $extensions;

    /**
     * ExtensionChain constructor.
     */
    public function __construct()
    {
        $this->extensions = new \SplPriorityQueue();
        $this->addExtension(new CoreExtension(), -20);
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options): array
    {
        foreach ($this->getExtensions() as $extension) {
            $options = $extension->buildOptions($options);
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildItem(ItemInterface $item, array $options): void
    {
        foreach ($this->getExtensions() as $extension) {
            $extension->buildItem($item, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension, int $priority = 0): void
    {
        $this->extensions->insert($extension, $priority);
    }

    private function getExtensions()
    {
        return clone $this->extensions;
    }
}
