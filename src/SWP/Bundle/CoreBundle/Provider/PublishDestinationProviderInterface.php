<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface PublishDestinationProviderInterface
{
    public function getDestinations(PackageInterface $package): array;

    public function countDestinations(PackageInterface $package): int;
}
