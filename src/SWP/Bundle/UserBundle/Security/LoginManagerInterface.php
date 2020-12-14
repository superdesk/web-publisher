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

namespace SWP\Bundle\UserBundle\Security;

use SWP\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

interface LoginManagerInterface
{
    /**
     * @param string $firewallName
     */
    public function logInUser($firewallName, UserInterface $user, Response $response = null);
}
