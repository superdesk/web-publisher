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

namespace SWP\Bundle\CoreBundle\Consumer;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SWP\Component\Bridge\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

class ContentPushConsumer implements ConsumerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PackageRepository
     */
    protected $packageRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var DataTransformerInterface
     */
    protected $jsonToPackageTransformer;

    /**
     * @var EntityManagerInterface
     */
    protected $packageObjectManager;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    public function __construct(
        LoggerInterface $logger,
        PackageRepository $packageRepository,
        EventDispatcherInterface $eventDispatcher,
        DataTransformerInterface $jsonToPackageTransformer,
        EntityManagerInterface $packageObjectManager,
        TenantContextInterface $tenantContext
    ) {
        $this->logger = $logger;
        $this->packageRepository = $packageRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->packageObjectManager = $packageObjectManager;
        $this->tenantContext = $tenantContext;
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            return $this->doExecute($msg);
        } catch (DBALException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }
    }

    public function doExecute(AMQPMessage $message): int
    {
        $decodedMessage = \unserialize($message->body);
        /** @var TenantInterface $tenant */
        $tenant = $decodedMessage['tenant'];
        /** @var PackageInterface $package */
        $package = $decodedMessage['package'];

        $this->tenantContext->setTenant($this->packageObjectManager->find(Tenant::class, $tenant->getId()));

        /** @var PackageInterface $existingPackage */
        $existingPackage = $this->findExistingPackage($package);
        if (null !== $existingPackage) {
            $package->setId($existingPackage->getId());
            $package->setCreatedAt($existingPackage->getCreatedAt());
            $package->setUpdatedAt(new \DateTime());
            $this->eventDispatcher->dispatch(Events::PACKAGE_PRE_UPDATE, new GenericEvent($package, [
                'eventName' => Events::PACKAGE_PRE_UPDATE,
                'package' => $existingPackage,
            ]));

            $package = $this->packageObjectManager->merge($package);
            $this->packageObjectManager->flush();

            $this->eventDispatcher->dispatch(Events::PACKAGE_POST_UPDATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_POST_UPDATE]));
            $this->eventDispatcher->dispatch(Events::PACKAGE_PROCESSED, new GenericEvent($package, ['eventName' => Events::PACKAGE_PROCESSED]));

            $this->reset();
            $this->logger->info(sprintf('Package %s was updated', $existingPackage->getGuid()));

            return ConsumerInterface::MSG_ACK;
        }

        $this->eventDispatcher->dispatch(Events::PACKAGE_PRE_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_PRE_CREATE]));
        $this->packageRepository->add($package);
        $this->eventDispatcher->dispatch(Events::PACKAGE_POST_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_POST_CREATE]));
        $this->eventDispatcher->dispatch(Events::PACKAGE_PROCESSED, new GenericEvent($package, ['eventName' => Events::PACKAGE_PROCESSED]));
        $this->reset();
        $this->logger->info(sprintf('Package %s was created', $package->getGuid()));

        return ConsumerInterface::MSG_ACK;
    }

    protected function findExistingPackage(PackageInterface $package)
    {
        $existingPackage = null;
        if (null === $package->getEvolvedFrom()) {
            $existingPackage = $this->packageRepository->findOneBy([
                'evolvedFrom' => $package->getGuid(),
            ]);
        }

        if (null === $existingPackage) {
            $existingPackage = $this->packageRepository->findOneBy(['guid' => $package->getEvolvedFrom() ?? $package->getGuid()]);
        }

        return $existingPackage;
    }

    private function reset(): void
    {
        $this->packageObjectManager->clear();
        if ($this->tenantContext instanceof ResettableInterface) {
            $this->tenantContext->reset();
        }
    }
}
