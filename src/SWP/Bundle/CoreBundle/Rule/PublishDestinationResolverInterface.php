<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Rule;

use SWP\Bundle\CoreBundle\Model\CompositePublishActionInterface;

interface PublishDestinationResolverInterface
{
    /**
     * @param string $tenant
     * @param int    $route
     *
     * @return CompositePublishActionInterface
     */
    public function resolve(string $tenant, int $route);
}
