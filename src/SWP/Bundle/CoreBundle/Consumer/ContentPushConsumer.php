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
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Sentry\Breadcrumb;
use Sentry\State\HubInterface;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Bundle\CoreBundle\Hydrator\PackageHydratorInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SWP\Component\Bridge\Events;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Lock\LockFactory;
use function unserialize;

class ContentPushConsumer implements ConsumerInterface
{
    protected $lockFactory;

    protected $logger;

    protected $packageRepository;

    protected $eventDispatcher;

    protected $jsonToPackageTransformer;

    protected $packageObjectManager;

    protected $tenantContext;

    protected $sentryHub;

    protected $packageHydrator;

    public function __construct(
        LockFactory $lockFactory,
        LoggerInterface $logger,
        PackageRepository $packageRepository,
        EventDispatcherInterface $eventDispatcher,
        DataTransformerInterface $jsonToPackageTransformer,
        EntityManagerInterface $packageObjectManager,
        TenantContextInterface $tenantContext,
        HubInterface $sentryHub,
        PackageHydratorInterface $packageHydrator
    ) {
        $this->lockFactory = $lockFactory;
        $this->logger = $logger;
        $this->packageRepository = $packageRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->jsonToPackageTransformer = $jsonToPackageTransformer;
        $this->packageObjectManager = $packageObjectManager;
        $this->tenantContext = $tenantContext;
        $this->sentryHub = $sentryHub;
        $this->packageHydrator = $packageHydrator;
    }

    public function execute(AMQPMessage $msg): int
    {
        $decodedMessage = unserialize($msg->body, [true]);
        /** @var TenantInterface $tenant */
        $tenant = $decodedMessage['tenant'];
        /** @var PackageInterface $package */
        $package = $decodedMessage['package'];
        $lock = $this->lockFactory->createLock(md5(json_encode(['type' => 'package', 'guid' => $package->getGuid()])), 120);

        try {
            if (!$lock->acquire()) {
                return ConsumerInterface::MSG_REJECT_REQUEUE;
            }

            return $this->doExecute($tenant, $package);
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

            return ConsumerInterface::MSG_REJECT;
        } finally {
            $lock->release();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NotNullConstraintViolationException
     * @throws DBALException
     * @throws ORMException
     * @throws Exception
     */
    public function doExecute(TenantInterface $tenant, PackageInterface $package): int
    {
        $packageType = $package->getType();
        if (ItemInterface::TYPE_TEXT !== $packageType && ItemInterface::TYPE_COMPOSITE !== $packageType) {
            return ConsumerInterface::MSG_REJECT;
        }

        $this->tenantContext->setTenant($this->packageObjectManager->find(Tenant::class, $tenant->getId()));

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

            return ConsumerInterface::MSG_ACK;
        }

        $this->eventDispatcher->dispatch(Events::PACKAGE_PRE_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_PRE_CREATE]));
        $this->packageRepository->add($package);
        $this->eventDispatcher->dispatch(Events::PACKAGE_POST_CREATE, new GenericEvent($package, ['eventName' => Events::PACKAGE_POST_CREATE]));
        $this->eventDispatcher->dispatch(Events::PACKAGE_PROCESSED, new GenericEvent($package, ['eventName' => Events::PACKAGE_PROCESSED]));
        $this->packageObjectManager->flush();

        $this->logger->info(sprintf('Package %s was created', $package->getGuid()));
        $this->reset();

        return ConsumerInterface::MSG_ACK;
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

    private function reset(): void
    {
        $this->packageObjectManager->clear();
        if ($this->tenantContext instanceof ResettableInterface) {
            $this->tenantContext->reset();
        }
    }

    private function logException(\Exception $e, PackageInterface $package, string $defaultMessage = 'Unhandled exception'): void
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
