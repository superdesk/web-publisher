<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Routing;

use SWP\Bundle\CoreBundle\Model\TenantInterface;
use Symfony\Component\Routing\RouterInterface;

final class TenantAwareAbsoluteUrlRouter
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function generate(
        string $name,
        TenantInterface $tenant,
        array $parameters = []
    ): string {
        $context = $this->router->getContext();
        $host = $tenant->getDomainName();
        if (null !== ($subdomain = $tenant->getSubdomain())) {
            $host = $subdomain.'.'.$host;
        }

        $context->setHost($host);

        return $this->router->generate($name, $parameters, RouterInterface::ABSOLUTE_URL);
    }
}
