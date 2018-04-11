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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\OutputChannel\Model\OutputChannel as BaseOutputChannel;

class OutputChannel extends BaseOutputChannel implements OutputChannelInterface
{
    /**
     * @var TenantInterface
     */
    protected $tenant;

    /**
     * {@inheritdoc}
     */
    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }
}
