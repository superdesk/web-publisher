<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle;

use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;

class SWPSeoBundle extends Bundle
{
    public function getSupportedDrivers(): array
    {
        return [
            Drivers::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function getModelClassNamespace(): string
    {
        return 'SWP\Component\Seo\Model';
    }
}
