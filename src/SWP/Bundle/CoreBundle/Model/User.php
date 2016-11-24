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

use FOS\UserBundle\Model\User as BaseUser;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Storage\Model\PersistableInterface;

class User extends BaseUser implements PersistableInterface, TenantAwareInterface, TimestampableInterface
{
    use TenantAwareTrait, TimestampableTrait;

    protected $id;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());

        parent::__construct();
    }
}
