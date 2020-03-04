<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\MessageHandler;

use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Sentry\Breadcrumb;
use Sentry\State\HubInterface;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Bundle\CoreBundle\Hydrator\PackageHydratorInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Component\Bridge\Events;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class AbstractContentPushHandler implements MessageHandlerInterface
{
    protected $logger;

    protected $packageRepository;

    protected $eventDispatcher;

    protected $jsonToPackageTransformer;

    protected $packageObjectManager;

    protected $tenantContext;

    protected $packageHydrator;

    public function __construct(
        LoggerInterface $logger,
        PackageRepository $packageRepository,
        EventDispatcherInterface $eventDispatcher,
        DataTransformerInterface $jsonToPackageTransformer,
        EntityManagerInterface $packageObjectManager,
        TenantContextInterface $tenantContext,
        HubInterface $sentryHub,
        PackageHydratorInterface $packageHydrator
    ) {
        $this->logger = $logger;
        $this->packageRepository = $packageRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->packageObjectManager = $packageObjectManager;
        $this->tenantContext = $tenantContext;
        $this->sentryHub = $sentryHub;
        $this->packageHydrator = $packageHydrator;
    }

    public function execute(int $tenantId, PackageInterface $package): void
    {
        try {
            $this->doExecute($tenantId, $package);
        } catch (NonUniqueResultException | NotNullConstraintViolationException $e) {
            $this->logException($e, $package, 'Unhandled NonUnique or NotNullConstraint exception');

            $cacheDriver = $this->packageObjectManager->getConfiguration()->getMetadataCacheImpl();
            $cacheDriver->flushAll();

            throw $e;
        } catch (DBALException | ORMException $e) {
            $this->logException($e, $package);

            throw $e;
        } catch (Exception $e) {
            $this->logException($e, $package);

            throw $e;
        } finally {
            $this->reset();
        }
    }

    private function doExecute(int $tenantId, PackageInterface $package): void
    {
        $packageType = $package->getType();
        if (ItemInterface::TYPE_TEXT !== $packageType && ItemInterface::TYPE_COMPOSITE !== $packageType) {
            return;
        }

        $this->tenantContext->setTenant($this->packageObjectManager->find(Tenant::class, $tenantId));

        /** @var PackageInterface $existingPackage */
        $existingPackage = $this->findExistingPackage($package);
        if (null !== $existingPackage) {
            $existingPackage = $this->packageHydrator->hydrate($package, $existingPackage);

            $this->eventDispatcher->dispatch(Events::PACKAGE_PRE_UPDATE, new GenericEvent($existingPackage, ['eventName' => Events::PACKAGE_PRE_UPDATE]));
            $this->packageObjectManager->flush();
            $this->eventDispatcher->dispatch(Events::PACKAGE_POST_UPDATE, new GenericEvent($existingPackage, ['eventName' => Events::PACKAGE_POST_UPDATE]));
            $this->eventDispatcher->dispatch(Events::PACKAGE_PROCESSED, new GenericEvent($existingPackage, ['eventName' => Events::PACKAGE_PROCESSED]));
            $this->packageObjectManager->flush();

            $this->reset();
            $this->logger->info(sprintf('Package %s was updated', $existingPackage->getGuid()));

            return;
        }

        $this->eventDispatcher->dispatch(Events::PACKAGE_PRE_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_PRE_CREATE]));
        $this->packageRepository->add($package);
        $this->eventDispatcher->dispatch(Events::PACKAGE_POST_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_POST_CREATE]));
        $this->eventDispatcher->dispatch(Events::PACKAGE_PROCESSED, new GenericEvent($package, ['eventName' => Events::PACKAGE_PROCESSED]));
        $this->packageObjectManager->flush();

        $this->logger->info(sprintf('Package %s was created', $package->getGuid()));
        $this->reset();
    }

    protected function findExistingPackage(PackageInterface $package)
    {
        $existingPackage = $this->packageRepository->findOneBy(['guid' => $package->getEvolvedFrom() ?? $package->getGuid()]);
        if (null === $existingPackage && null !== $package->getEvolvedFrom()) {
            $existingPackage = $this->packageRepository->findOneBy(['guid' => $package->getGuid()]);
        }

        if (null === $existingPackage) {
            // check for updated items (with evolved from)
            $existingPackage = $this->packageRepository->findOneBy(['evolvedFrom' => $package->getGuid()]);
        }

        return $existingPackage;
    }

    protected function reset(): void
    {
        $this->packageObjectManager->clear();
        if ($this->tenantContext instanceof ResettableInterface) {
            $this->tenantContext->reset();
        }
    }

    protected function logException(\Exception $e, PackageInterface $package, string $defaultMessage = 'Unhandled exception'): void
    {
        $this->logger->error('' !== $e->getMessage() ? $e->getMessage() : $defaultMessage, ['trace' => $e->getTraceAsString()]);
        $this->sentryHub->addBreadcrumb(new Breadcrumb(
            Breadcrumb::LEVEL_DEBUG,
            Breadcrumb::TYPE_DEFAULT,
            'publishing',
            'Package',
            [
                'guid' => $package->getGuid(),
                'headline' => $package->getHeadline(),
            ]
        ));
        $this->sentryHub->captureException($e);
    }
}
