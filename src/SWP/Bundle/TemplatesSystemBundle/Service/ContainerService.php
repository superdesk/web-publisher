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

use SWP\Bundle\TemplatesSystemBundle\Container\SimpleContainer;
use SWP\Bundle\TemplatesSystemBundle\Model\Container;
use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContainerService
{
    const OPEN_TAG_TEMPLATE = '<div id="swp_container_{{ id }}" class="swp_container {{ class }}" style="{% if height %}height: {{ height }}px;{% endif %}{% if width %}width: {{width}}px;{% endif %}{{styles}}"{% for value in data %} data-{{value.getKey()}}="{{value.getValue()}}"{% endfor %} >';
    const CLOSE_TAG_TEMPLATE = '</div>';

    protected $serviceContainer;
    protected $objectManager;
    protected $cacheDir;
    protected $debug;
    protected $renderer = false;
    protected $eventDispatcher;

    /**
     * ContainerService constructor.
     *
     * @param RegistryInterface        $registry
     * @param EventDispatcherInterface $eventDispatcher
     * @param ContainerInterface       $serviceContainer
     * @param string                   $cacheDir
     * @param bool                     $debug
     */
    public function __construct(RegistryInterface $registry, EventDispatcherInterface $eventDispatcher, ContainerInterface $serviceContainer, $cacheDir, $debug = false)
    {
        $this->objectManager = $registry->getManager();
        $this->cacheDir = $cacheDir.'/twig';
        $this->debug = $debug;
        $this->eventDispatcher = $eventDispatcher;
        $this->serviceContainer = $serviceContainer;
    }

    public function getContainer($name, array $parameters = [], $createIfNotExists = true)
    {
        $containerEntity = $this->objectManager->getRepository('SWP\Bundle\TemplatesSystemBundle\Model\Container')
            ->getByName($name)
            ->getOneOrNullResult();

        if (!$containerEntity && $createIfNotExists) {
            $containerEntity = $this->createNewContainer($name, $parameters);
        } elseif (!$containerEntity) {
            throw new \Exception('Container was not found');
        }

        $widgets = [];
        $containerWidgets = $this->objectManager->getRepository('SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget')
            ->getSortedWidgets(['container' => $containerEntity])
            ->getResult();

        foreach ($containerWidgets as $containerWidget) {
            $widgetModel = $containerWidget->getWidget();
            $widgetClass = $widgetModel->getType();

            if (is_a($widgetClass, TemplatingWidgetHandler::class, true)) {
                $widgetHandler = new $widgetClass($widgetModel, $this->serviceContainer);
            } else {
                $widgetHandler = new $widgetClass($widgetModel);
            }

            $widgets[] = $widgetHandler;
        }

        $container = new SimpleContainer($containerEntity, $this->getRenderer());
        $container->setWidgets($widgets);

        return $container;
    }

    public function getRenderer()
    {
        if ($this->renderer !== false) {
            return $this->renderer;
        }

        $options = [];
        if (false === $this->debug) {
            $options['cache'] = $this->cacheDir;
        }

        $this->renderer = new \Twig_Environment(
            new \Twig_Loader_Array([
                'open_tag' => self::OPEN_TAG_TEMPLATE,
                'close_tag' => self::CLOSE_TAG_TEMPLATE,
            ]), $options
        );

        return $this->renderer;
    }

    public function createNewContainer($name, array $parameters = [])
    {
        $containerEntity = $this->serviceContainer->get('swp.factory.container')->create();
        $containerDataFactory = $this->serviceContainer->get('swp.factory.container_data');
        $containerEntity->setName($name);
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'height':
                    $containerEntity->setHeight($value);
                    break;
                case 'width':
                    $containerEntity->setWidth($value);
                    break;
                case 'cssClass':
                    $containerEntity->setCssClass($value);
                    break;
                case 'styles':
                    $containerEntity->setStyles($value);
                    break;
                case 'visible':
                    $containerEntity->setVisible($value);
                    break;
                case 'data':
                    foreach ($value as $dataKey => $dataValue) {
                        $containerData = $containerDataFactory->create($dataKey, $dataValue);
                        $containerData->setContainer($containerEntity);
                        $this->objectManager->persist($containerData);
                        $containerEntity->addData($containerData);
                    }
            }
        }
        $this->objectManager->persist($containerEntity);
        $this->objectManager->flush();

        $this->eventDispatcher
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($containerEntity));

        return $containerEntity;
    }
}
