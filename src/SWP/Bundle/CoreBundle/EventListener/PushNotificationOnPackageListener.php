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

use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Common\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PushNotificationOnPackageListener
{
    public const PACKAGE_STATE_UPDATE = 'update';

    public const PACKAGE_STATE_CREATE = 'create';

    /**
     * @var PusherInterface
     */
    private $pusher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * PushNotificationOnPackageProcessedListener constructor.
     *
     * @param PusherInterface     $pusher
     * @param SerializerInterface $serializer
     */
    public function __construct(PusherInterface $pusher, SerializerInterface $serializer)
    {
        $this->pusher = $pusher;
        $this->serializer = $serializer;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \SWP\Component\Common\Exception\UnexpectedTypeException
     */
    public function onPostCreate(GenericEvent $event): void
    {
        $package = $this->getPackage($event);

        $this->pushNotification($package, self::PACKAGE_STATE_CREATE);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \SWP\Component\Common\Exception\UnexpectedTypeException
     */
    public function onPostUpdate(GenericEvent $event): void
    {
        $package = $this->getPackage($event);

        $this->pushNotification($package, self::PACKAGE_STATE_UPDATE);
    }

    private function pushNotification(PackageInterface $package, string $state)
    {
//        $this->pusher->push([
//            'package' => json_decode($this->serializer->serialize($package, 'json'), true),
//            'state' => $state,
//        ],
//            'package_created'
//        );
    }

    /**
     * @param GenericEvent $event
     *
     * @return PackageInterface
     *
     * @throws \SWP\Component\Common\Exception\UnexpectedTypeException
     */
    private function getPackage(GenericEvent $event): PackageInterface
    {
        /** @var PackageInterface $package */
        if (!($package = $event->getSubject()) instanceof PackageInterface) {
            throw UnexpectedTypeException::unexpectedType(
                \is_object($package) ? \get_class($package) : \gettype($package),
                PackageInterface::class
            );
        }

        return $package;
    }
}
