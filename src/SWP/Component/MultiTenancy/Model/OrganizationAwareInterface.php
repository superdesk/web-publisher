<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Model;

interface OrganizationAwareInterface
{
    /**
     * @return OrganizationInterface|null
     */
    public function getOrganization();

    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization);
}
