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

use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerWidgetFactoryInterface;
use SWP\Bundle\TemplatesSystemBundle\Form\Type\WidgetType;
use SWP\Bundle\TemplatesSystemBundle\Provider\ContainerProviderInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ThemeWidgetsGenerator implements GeneratorInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FactoryInterface
     */
    protected $widgetModelFactory;

    /**
     * @var RepositoryInterface
     */
    protected $widgetModelRepository;

    /**
     * @var ContainerWidgetFactoryInterface
     */
    protected $containerWidgetFactory;

    /**
     * @var ContainerProviderInterface
     */
    protected $containerProvider;

    /**
     * ThemeWidgetsGenerator constructor.
     *
     * @param FormFactoryInterface            $formFactory
     * @param FactoryInterface                $widgetModelFactory
     * @param RepositoryInterface             $widgetModelRepository
     * @param ContainerWidgetFactoryInterface $containerWidgetFactory
     * @param ContainerProviderInterface      $containerProvider
     */
    public function __construct(FormFactoryInterface $formFactory, FactoryInterface $widgetModelFactory, RepositoryInterface $widgetModelRepository, ContainerWidgetFactoryInterface $containerWidgetFactory, ContainerProviderInterface $containerProvider)
    {
        $this->formFactory = $formFactory;
        $this->widgetModelFactory = $widgetModelFactory;
        $this->widgetModelRepository = $widgetModelRepository;
        $this->containerWidgetFactory = $containerWidgetFactory;
        $this->containerProvider = $containerProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $widgets): void
    {
        foreach ($widgets as $widgetData) {
            if (null !== $this->widgetModelRepository->findOneByName($widgetData['name'])) {
                continue;
            }

            $widgetContainers = 0;
            if (count($widgetData['containers']) > 0) {
                $widgetContainers = $widgetData['containers'];
            }
            unset($widgetData['containers']);

            $widget = $this->createWidget($widgetData);
            if (null !== $widgetContainers) {
                $this->linkWidgets($widget, $widgetContainers);
            }
            $this->widgetModelRepository->add($widget);
        }
    }

    /**
     * @param array $widgetData
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createWidget(array $widgetData)
    {
        $widget = $this->widgetModelFactory->create();
        $form = $this->formFactory->create(WidgetType::class, $widget);
        $form->submit($widgetData, false);
        if (!$form->isValid()) {
            throw new \Exception('Invalid widget definition');
        }

        return $widget;
    }

    /**
     * @param $widget
     * @param $containers
     */
    protected function linkWidgets($widget, $containers)
    {
        foreach ($containers as $containerName) {
            $container = $this->containerProvider->getOneByName($containerName);
            if (null === $container) {
                continue;
            }

            $containerWidget = $this->containerWidgetFactory->create($container, $widget);
            $this->widgetModelRepository->persist($containerWidget);

            $container->addWidget($containerWidget);
        }
    }
}
