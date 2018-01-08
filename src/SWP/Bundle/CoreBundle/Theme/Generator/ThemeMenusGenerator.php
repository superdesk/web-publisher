<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Theme\Generator;

use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\CoreBundle\Model\MenuItemInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Factory\MenuFactoryInterface;
use SWP\Bundle\MenuBundle\Form\Type\MenuType;
use SWP\Bundle\MenuBundle\Manager\MenuItemManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ThemeMenusGenerator implements GeneratorInterface
{
    /**
     * @var RouteProviderInterface
     */
    protected $routeProvider;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var MenuFactoryInterface
     */
    protected $menuFactory;

    /**
     * @var MenuItemRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var MenuItemManagerInterface
     */
    protected $menuManager;

    /**
     * ThemeMenusGenerator constructor.
     *
     * @param RouteProviderInterface      $routeProvider
     * @param FormFactoryInterface        $formFactory
     * @param MenuFactoryInterface        $menuFactory
     * @param MenuItemRepositoryInterface $menuRepository
     * @param MenuItemManagerInterface    $menuManager
     */
    public function __construct(RouteProviderInterface $routeProvider, FormFactoryInterface $formFactory, MenuFactoryInterface $menuFactory, MenuItemRepositoryInterface $menuRepository, MenuItemManagerInterface $menuManager)
    {
        $this->routeProvider = $routeProvider;
        $this->formFactory = $formFactory;
        $this->menuFactory = $menuFactory;
        $this->menuRepository = $menuRepository;
        $this->menuManager = $menuManager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $menus, MenuItemInterface $parent = null): void
    {
        foreach ($menus as $menuData) {
            if (null !== $this->menuRepository->findOneBy(['name' => $menuData['name'], 'parent' => $parent])) {
                continue;
            }

            $this->handleMenu($menuData, $parent);
        }
    }

    /**
     * @param array                  $menuData
     * @param MenuItemInterface|null $parent
     *
     * @throws \Exception
     */
    private function handleMenu(array $menuData, MenuItemInterface $parent = null)
    {
        $children = null;
        if (isset($menuData['children']) && count($menuData['children']) > 0) {
            $children = $menuData['children'];
        }
        unset($menuData['children']);

        if (null !== $parent) {
            $menuData['parent'] = $parent->getId();
        }

        $menu = $this->createMenu($menuData);
        $this->menuRepository->add($menu);

        if (null !== $children) {
            $this->generate($children, $menu);
        }
    }

    /**
     * @param array $menuData
     *
     * @return MenuItemInterface
     *
     * @throws \Exception
     */
    private function createMenu(array $menuData): MenuItemInterface
    {
        /** @var MenuItemInterface $menuItem */
        $menuItem = $this->menuFactory->createItem($menuData['name']);

        if (null !== $menuData['route'] && null !== $route = $this->routeProvider->getRouteByName($menuData['route'])) {
            $menuItem->setRoute($route);
        }
        unset($menuData['route']);

        $form = $this->formFactory->create(MenuType::class, $menuItem);
        $form->submit($menuData, false);
        if (!$form->isValid()) {
            throw new \Exception('Invalid menu definition');
        }

        $this->menuManager->update($menuItem);

        return $menuItem;
    }
}
