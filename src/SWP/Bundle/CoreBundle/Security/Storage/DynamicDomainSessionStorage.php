<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Security\Storage;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class DynamicDomainSessionStorage extends NativeSessionStorage
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $tenant = $this->tenantContext->getTenant();
        if (null !== $tenant) {
            $options['cookie_domain'] = '.'.$tenant->getDomainName();
        }

        $options['cookie_httponly'] = true;
        $options['name'] = 'SUPERDESKPUBLISHER';

        parent::setOptions($options);
    }
}
