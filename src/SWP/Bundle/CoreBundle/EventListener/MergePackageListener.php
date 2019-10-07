<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
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

use DateTime;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MergePackageListener
{
    public function preUpdate(GenericEvent $event): void
    {
        $package = $event->getSubject();

        if (!$package instanceof PackageInterface) {
            throw new UnexpectedTypeException($package, PackageInterface::class);
        }

        $existingPackage = $event->getArgument('package');

        $package->setStatus($existingPackage->getStatus());
        $package->setId($existingPackage->getId());
        $package->setCreatedAt($existingPackage->getCreatedAt());
        $package->setUpdatedAt(new DateTime());
    }
}
