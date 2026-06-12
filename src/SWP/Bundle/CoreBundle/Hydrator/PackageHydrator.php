<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Hydrator;

use DateTime;
use SWP\Bundle\CoreBundle\Model\PackageInterface;

final class PackageHydrator implements PackageHydratorInterface
{
    public function hydrate(PackageInterface $newPackage, PackageInterface $existingPackage): PackageInterface
    {
        $newPackage->setCreatedAt($existingPackage->getCreatedAt());
        $newPackage->setUpdatedAt(new DateTime());
        $newPackage->setStatus($existingPackage->getStatus());

        if (null !== $newPackage->getId()) {
            return $existingPackage;
        }

        $newPackage->setId($existingPackage->getId());

        // set package when the item or group is newly added
        foreach ($newPackage->getGroups() as $group) {
            $group->setPackage($existingPackage);
        }

        foreach ($newPackage->getItems() as $item) {
            $item->setPackage($existingPackage);
        }

        $this->copyProperties($newPackage, $existingPackage);

        return $existingPackage;
    }

    /**
     * Copies all instance properties (including inherited private ones),
     * replacing the previously used ocramius/generated-hydrator.
     */
    private function copyProperties(object $from, object $to): void
    {
        $reflection = new \ReflectionObject($from);

        do {
            foreach ($reflection->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }

                if (!$property->isInitialized($from)) {
                    continue;
                }

                $property->setValue($to, $property->getValue($from));
            }
        } while ($reflection = $reflection->getParentClass());
    }
}
