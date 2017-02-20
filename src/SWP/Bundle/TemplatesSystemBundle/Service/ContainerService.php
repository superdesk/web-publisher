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

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerDataFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerDataInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as ServiceContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Class ContainerService.
 */
class ContainerService implements ContainerServiceInterface
{
    /**
     * @var ServiceContainerInterface
     */
    protected $serviceContainer;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ContainerService constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param EventDispatcherInterface  $eventDispatcher
     * @param ServiceContainerInterface $serviceContainer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ServiceContainerInterface $serviceContainer
    ) {
        $this->entityManager = $entityManager;
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
                        $this->entityManager->persist($containerData);
                        $container->addData($containerData);
                    }
            }
        }
        $this->entityManager->persist($container);
        $this->entityManager->flush();

        $this->eventDispatcher
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($container));

        return $container;
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
                $this->entityManager->remove($containerData);
            }

            // Apply new containerData's
            foreach ($extraData as $key => $value) {
                /** @var ContainerDataInterface $containerData */
                $containerData = $containerDataFactory->create($key, $value);
                $containerData->setContainer($container);
                $this->entityManager->persist($containerData);
                $container->addData($containerData);
            }
        }

        $this->entityManager->flush();
        $this->entityManager->refresh($container);

        return $container;
    }

    /**
     * @param mixed              $object
     * @param ContainerInterface $container
     * @param Request            $request
     *
     * @throws \Exception
     */
    public function linkUnlinkWidget($object, ContainerInterface $container, Request $request)
    {
        $containerWidget = $this->serviceContainer->get('swp.repository.container_widget')
            ->findOneBy([
                'widget' => $object,
                'container' => $container,
            ]);

        if ($request->getMethod() === 'LINK') {
            $position = false;
            if (count($notConvertedLinks = self::getNotConvertedLinks($request)) > 0) {
                foreach ($notConvertedLinks as $link) {
                    if (isset($link['resourceType']) && $link['resourceType'] == 'widget-position') {
                        $position = $link['resource'];
                    }
                }
            }

            if ($position === false && $containerWidget) {
                throw new ConflictHttpException('WidgetModel is already linked to container');
            }

            if (!$containerWidget) {
                $containerWidget = $this->serviceContainer->get('swp.factory.container_widget')->create($container, $object);
                $this->entityManager->persist($containerWidget);
            }

            if ($position !== false) {
                $containerWidget->setPosition($position);
            }
            $container->addWidget($containerWidget);
            $this->entityManager->flush();
        } elseif ($request->getMethod() === 'UNLINK') {
            if (!$container->getWidgets()->contains($containerWidget)) {
                throw new ConflictHttpException('WidgetModel is not linked to container');
            }
            $this->entityManager->remove($containerWidget);
        }

        return $container;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public static function getNotConvertedLinks($request)
    {
        $links = [];
        foreach ($request->attributes->get('links') as $idx => $link) {
            if (is_string($link)) {
                $linkParams = explode(';', trim($link));
                $resourceType = null;
                if (count($linkParams) > 1) {
                    $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                    $resourceType = str_replace('"', '', str_replace('rel=', '', $resourceType));
                }
                $resource = array_shift($linkParams);
                $resource = preg_replace('/<|>/', '', $resource);

                $links[] = [
                    'resource' => $resource,
                    'resourceType' => $resourceType,
                ];
            }
        }

        return $links;
    }
}
