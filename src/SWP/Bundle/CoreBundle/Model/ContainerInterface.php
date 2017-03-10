<?php

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

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface as BaseContainerInterface;

interface ContainerInterface extends BaseContainerInterface, TenantAwareInterface, RevisionAwareInterface, PersistableInterface
{
}
