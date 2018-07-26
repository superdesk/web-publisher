<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Service;

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRenderer;
use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerRendererFactory;
use SWP\Bundle\TemplatesSystemBundle\Provider\ContainerProviderInterface;
use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as ServiceContainerInterface;

/**
 * Class RendererService.
 */
class RendererService implements RendererServiceInterface
{
    /**
     * @var ServiceContainerInterface
     */
    protected $serviceContainer;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var ContainerServiceInterface
     */
    protected $templateContainerService;

    /**
     * @var ContainerProviderInterface
     */
    protected $containerProvider;

    /**
     * @var ContainerRendererFactory
     */
    protected $containerRendererFactory;

    /**
     * RendererService constructor.
     *
     * @param ServiceContainerInterface  $serviceContainer
     * @param string                     $cacheDir
     * @param bool                       $debug
     * @param ContainerServiceInterface  $templateContainerService
     * @param ContainerProviderInterface $containerProvider
     * @param ContainerRendererFactory   $containerRendererFactory
     */
    public function __construct(
        ServiceContainerInterface $serviceContainer,
        string $cacheDir,
        bool $debug,
        ContainerServiceInterface $templateContainerService,
        ContainerProviderInterface $containerProvider,
        ContainerRendererFactory $containerRendererFactory
    ) {
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
        $this->serviceContainer = $serviceContainer;
        $this->templateContainerService = $templateContainerService;
        $this->containerProvider = $containerProvider;
        $this->containerRendererFactory = $containerRendererFactory;
    }

    public function getContainerRenderer(string $name, array $parameters = [], $createIfNotExists = true, ContainerInterface $container = null): ContainerRenderer
    {
        if (!$container instanceof ContainerInterface) {
            $container = $this->containerProvider->getOneByName($name);
            if (!$container && $createIfNotExists) {
                $container = $this->templateContainerService->createContainer($name, $parameters);
            } elseif (!$container) {
                throw new \Exception('Container was not found');
            }
        }

        $containerRenderer = $this->containerRendererFactory->create($container, null, $this->debug, $this->cacheDir);
        $widgets = $this->initializeWidgets($this->containerProvider->getContainerWidgets($container));
        $containerRenderer->setWidgets($widgets);

        return $containerRenderer;
    }

    /**
     * @param $containerWidgets
     *
     * @return array
     */
    private function initializeWidgets($containerWidgets)
    {
        $widgets = [];
        foreach ($containerWidgets as $widget) {
            $widgetModel = $widget->getWidget();
            $widgetClass = $widgetModel->getType();

            if (is_a($widgetClass, TemplatingWidgetHandler::class, true)) {
                $widgetHandler = new $widgetClass($widgetModel, $this->serviceContainer);
            } else {
                $widgetHandler = new $widgetClass($widgetModel);
            }

            $widgets[] = $widgetHandler;
        }

        return $widgets;
    }
}
