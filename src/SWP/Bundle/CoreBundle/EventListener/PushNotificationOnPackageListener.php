<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Common\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class PushNotificationOnPackageListener
{
    public const PACKAGE_STATE_UPDATE = 'update';

    public const PACKAGE_STATE_CREATE = 'create';

    public const TOPIC = 'swp/package';

    private HubInterface $hub;

    private SerializerInterface $serializer;

    private LoggerInterface $logger;

    public function __construct(HubInterface $hub, SerializerInterface $serializer, ?LoggerInterface $logger = null)
    {
        $this->hub = $hub;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    public function onPostCreate(GenericEvent $event): void
    {
        $package = $this->getPackage($event);

        $this->pushNotification($package, self::PACKAGE_STATE_CREATE);
    }

    public function onPostUpdate(GenericEvent $event): void
    {
        $package = $this->getPackage($event);

        $this->pushNotification($package, self::PACKAGE_STATE_UPDATE);
    }

    private function pushNotification(PackageInterface $package, string $state): void
    {
        try {
            $this->hub->publish(new Update(
                self::TOPIC,
                json_encode([
                    'package' => json_decode($this->serializer->serialize($package, 'json'), true),
                    'state' => $state,
                ], JSON_THROW_ON_ERROR),
                true
            ));
        } catch (\Throwable $e) {
            // A failing realtime notification must never break content push.
            $this->logger->error('Could not publish package update to the Mercure hub.', ['exception' => $e]);
        }
    }

    private function getPackage(GenericEvent $event): PackageInterface
    {
        /** @var PackageInterface $package */
        if (!($package = $event->getSubject()) instanceof PackageInterface) {
            throw UnexpectedTypeException::unexpectedType(\is_object($package) ? \get_class($package) : \gettype($package), PackageInterface::class);
        }

        return $package;
    }
}
