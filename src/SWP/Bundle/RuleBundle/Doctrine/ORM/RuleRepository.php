<?php

/*
 * This file is part of the Superdesk Web Publisher Rules Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RuleBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;

class RuleRepository extends EntityRepository implements RuleRepositoryInterface
{
}
