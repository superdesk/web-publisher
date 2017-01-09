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
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerService;
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerServiceInterface;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
     * @param RegistryInterface         $registry
     * @param EventDispatcherInterface  $eventDispatcher
     * @param ServiceContainerInterface $serviceContainer
     */
    public function __construct(
        RegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        ServiceContainerInterface $serviceContainer
    ) {
        parent::__construct($registry, $eventDispatcher, $serviceContainer);
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
            // TODO: check if there is no version of that container for working revision

            $entityManager = $this->serviceContainer->get('doctrine')->getManager();
            if ($entityManager->contains($container)) {
                $entityManager->detach($container);
            }

            $workingContainer = $container->fork();
            $workingContainer->setWidgets(new ArrayCollection());
            $workingContainer->setData(new ArrayCollection());
            /** @var RevisionAwareInterface $workingContainer */
            $revisionContext = $this->serviceContainer->get('swp_revision.context.revision');
            $workingContainer->setRevision($revisionContext->getWorkingRevision());
            $entityManager->persist($workingContainer);

            return parent::updateContainer($workingContainer, $extraData);
        }

        return parent::updateContainer($container, $extraData);
    }
}
