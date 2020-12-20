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

namespace SWP\Bundle\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Response user event that allows null user.
 *
 */
class GetResponseNullableUserEvent extends GetResponseUserEvent
{
    /**
     * GetResponseNullableUserEvent constructor.
     */
    public function __construct(UserInterface $user = null, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }
}
