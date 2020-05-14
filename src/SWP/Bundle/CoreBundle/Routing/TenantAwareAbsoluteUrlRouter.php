<?php

declare(strict_types=1);

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
