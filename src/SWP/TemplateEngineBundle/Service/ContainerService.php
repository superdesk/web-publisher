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

use SWP\TemplatesSystem\Gimme\Container\SimpleContainer;
use SWP\TemplateEngineBundle\Model\Container;

class ContainerService
{
    protected $objectManager;

    public function __construct($doctrine)
    {
        $this->objectManager = $doctrine->getManager();
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

        $container = new SimpleContainer($containerEntity);
        $container->setWidgets($widgets);

        return $container;
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
            }
        }
        $this->objectManager->persist($containerEntity);
        $this->objectManager->flush();

        return $containerEntity;
    }
}
