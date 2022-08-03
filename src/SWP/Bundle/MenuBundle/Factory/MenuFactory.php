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

namespace SWP\Bundle\MenuBundle\Factory;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var ExtensionInterface
     */
    private $extensionChain;

    /**
     * MenuFactory constructor.
     *
     * @param FactoryInterface   $decoratedFactory
     * @param ExtensionInterface $extensionChain
     */
    public function __construct(FactoryInterface $decoratedFactory, ExtensionInterface $extensionChain)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->extensionChain = $extensionChain;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->decoratedFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($name, array $options = []): ItemInterface
    {
        /** @var MenuItemInterface $item */
        $item = $this->create();
        $item->setName($name);

        $options = $this->extensionChain->buildOptions($options);

        $this->extensionChain->buildItem($item, $options);

        return $item;
    }
}
