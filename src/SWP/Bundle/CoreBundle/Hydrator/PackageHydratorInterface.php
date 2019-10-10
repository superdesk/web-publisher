<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Hydrator;

use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface PackageHydratorInterface
{
    public function hydrate(PackageInterface $newPackage, PackageInterface $existingPackage): PackageInterface;
}
