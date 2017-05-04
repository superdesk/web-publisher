<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Rule\Model\Rule as BaseRule;

class Rule extends BaseRule implements RuleInterface
{
    use TenantAwareTrait, OrganizationAwareTrait;
}
