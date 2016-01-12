<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\TemplateEngineBundle\Service;

use SWP\TemplateEngineBundle\Container\SimpleContainer;
use SWP\TemplateEngineBundle\Model\Container;
use SWP\TemplateEngineBundle\Model\ContainerData;

class ContainerService
{
    const OPEN_TAG_TEMPLATE = '<div id="swp_container_{{ id }}" class="swp_container {{ class }}" style="{% if height %}height: {{ height }}px;{% endif %}{% if width %}width: {{width}}px;{% endif %}{{styles}}"{% for value in data %} data-{{value.getKey()}}="{{value.getValue()}}"{% endfor %} >';
    const CLOSE_TAG_TEMPLATE = '</div>';

    protected $objectManager;
    protected $cacheDir = false;

    public function __construct($doctrine, $cacheDir, $debug)
    {
        $this->objectManager = $doctrine->getManager();
        if (!$debug) {
            $this->cacheDir = $cacheDir.'/twig';
        }
    }

    public function getContainer($name, array $parameters, $createIfNotExists = true)
    {
        $containerEntity = $this->objectManager->getRepository('SWP\TemplateEngineBundle\Model\Container')
            ->getByName($name)
            ->getOneOrNullResult();

        if (!$containerEntity && $createIfNotExists) {
            $containerEntity = $this->createNewContainer($name, $parameters);
        }

        $widgets = [];
        $containerWidgets = $this->objectManager->getRepository('SWP\TemplateEngineBundle\Model\ContainerWidget')
            ->getSortedWidgets(['container' => $containerEntity])
            ->getResult();

        foreach ($containerWidgets as $containerWidget) {
            $widgetModel = $containerWidget->getWidget();
            $widgetClass = $widgetModel->getType();
            $widgets[] = new $widgetClass($widgetModel);
        }

        $container = new SimpleContainer($containerEntity, $this->getRenderer());
        $container->setWidgets($widgets);

        return $container;
    }

    public function getRenderer()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Array([
                'open_tag' => self::OPEN_TAG_TEMPLATE,
                'close_tag' => self::CLOSE_TAG_TEMPLATE,
            ]), [
                'cache' => $this->cacheDir,
            ]
        );
    }

    public function createNewContainer($name, array $parameters = array())
    {
        $containerEntity = new Container();
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
                        $containerData = new ContainerData($dataKey, $dataValue);
                        $containerData->setContainer($containerEntity);
                        $this->objectManager->persist($containerData);
                        $containerEntity->addData($containerData);
                    }
            }
        }
        $this->objectManager->persist($containerEntity);
        $this->objectManager->flush();

        return $containerEntity;
    }
}
