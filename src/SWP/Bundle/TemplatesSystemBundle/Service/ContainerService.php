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

use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerDataFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerDataInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as ServiceContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RendererService.
 */
class ContainerService implements ContainerServiceInterface
{
    /**
     * @var ServiceContainerInterface
     */
    protected $serviceContainer;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * RendererService constructor.
     *
     * @param RegistryInterface         $registry
     * @param EventDispatcherInterface  $eventDispatcher
     * @param ServiceContainerInterface $serviceContainer
     */
    public function __construct(
        RegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        ServiceContainerInterface $serviceContainer
    ) {
        $this->objectManager = $registry->getManager();
        $this->eventDispatcher = $eventDispatcher;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer($name, array $parameters = [], ContainerInterface $container = null): ContainerInterface
    {
        if (null === $container) {
            /** @var ContainerInterface $containerEntity */
            $container = $this->serviceContainer->get('swp.factory.container')->create();
        }

        $containerDataFactory = $this->serviceContainer->get('swp.factory.container_data');
        $container->setName($name);
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'cssClass':
                    $container->setCssClass($value);
                    break;
                case 'styles':
                    $container->setStyles($value);
                    break;
                case 'visible':
                    $container->setVisible($value);
                    break;
                case 'data':
                    foreach ($value as $dataKey => $dataValue) {
                        /** @var ContainerDataInterface $containerData */
                        $containerData = $containerDataFactory->create($dataKey, $dataValue);
                        $containerData->setContainer($container);
                        $this->objectManager->persist($containerData);
                        $containerEntity->addData($containerData);
                    }
            }
        }
        $this->objectManager->persist($container);
        $this->objectManager->flush();

        $this->eventDispatcher
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($containerEntity));

        return $containerEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function updateContainer(ContainerInterface $container, array $extraData): ContainerInterface
    {
        /** @var ContainerDataFactoryInterface $containerDataFactory */
        $containerDataFactory = $this->serviceContainer->get('swp.factory.container_data');
        if (!empty($extraData) && is_array($extraData)) {
            // Remove old containerData's
            foreach ($container->getData() as $containerData) {
                $this->objectManager->remove($containerData);
            }

            // Apply new containerData's
            foreach ($extraData as $key => $value) {
                /** @var ContainerDataInterface $containerData */
                $containerData = $containerDataFactory->create($key, $value);
                $containerData->setContainer($container);
                $this->objectManager->persist($containerData);
                $container->addData($containerData);
            }
        }

        $this->objectManager->flush();
        $this->objectManager->refresh($container);

        return $container;
    }
}
