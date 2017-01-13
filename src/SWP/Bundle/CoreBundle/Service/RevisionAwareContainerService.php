<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerService;
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerServiceInterface;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerWidgetInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as ServiceContainerInterface;

/**
 * Class RendererService.
 */
class RevisionAwareContainerService extends ContainerService implements ContainerServiceInterface
{
    /**
     * RevisionAwareContainerService constructor.
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
        parent::__construct($entityManager, $eventDispatcher, $serviceContainer);
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer($name, array $parameters = [], ContainerInterface $container = null): ContainerInterface
    {
        // assign current revision to container
        $container = $this->serviceContainer->get('swp.factory.container')->create();
        if ($container instanceof RevisionAwareInterface) {
            $revisionContext = $this->serviceContainer->get('swp_revision.context.revision');
            $container->setRevision($revisionContext->getWorkingRevision());
        }

        return parent::createContainer($name, $parameters, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function updateContainer(ContainerInterface $container, array $extraData): ContainerInterface
    {
        if ($container instanceof RevisionAwareInterface &&
            $container->getRevision()->getStatus() === RevisionInterface::STATE_PUBLISHED
        ) {
            if ($this->entityManager->contains($container)) {
                $this->entityManager->detach($container);
            }

            $workingContainer = $container->fork();
            $workingContainer->setWidgets(new ArrayCollection());
            $workingContainer->setData(new ArrayCollection());
            /** @var RevisionAwareInterface | ContainerInterface $workingContainer */
            $revisionContext = $this->serviceContainer->get('swp_revision.context.revision');
            $workingContainer->setRevision($revisionContext->getWorkingRevision());
            $this->entityManager->persist($workingContainer);

            $workingContainer = parent::updateContainer($workingContainer, $extraData);
            $this->forkContainerRelations($container, $workingContainer);

            return $workingContainer;
        }

        return parent::updateContainer($container, $extraData);
    }

    private function forkContainerRelations(ContainerInterface $container, ContainerInterface $workingContainer)
    {
        $containerWidgetFactory = $this->serviceContainer->get('swp.factory.container_widget');
        /** @var ContainerWidgetInterface $containerWidget */
        foreach ($container->getWidgets() as $containerWidget) {
            $containerWidget = $containerWidgetFactory->create($workingContainer, $containerWidget->getWidget());
            $this->entityManager->persist($containerWidget);
            $workingContainer->addWidget($containerWidget);
        }
    }
}
