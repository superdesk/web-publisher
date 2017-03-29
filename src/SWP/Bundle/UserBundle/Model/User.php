<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use SWP\Component\Common\Model\TimestampableTrait;

class User extends BaseUser implements UserInterface
{
    use TimestampableTrait;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());

        parent::__construct();
    }
}
