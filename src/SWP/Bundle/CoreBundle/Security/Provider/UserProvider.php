<?php

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

namespace SWP\Bundle\CoreBundle\Security\Provider;

use SWP\Bundle\UserBundle\Security\UserProvider as BaseUserProvider;

class UserProvider extends BaseUserProvider
{
    public function findOneByEmail($email)
    {
        return $this->userManager->findUserBy([
            'email' => $email,
        ]);
    }

    public function findOneByExternalId($externalId)
    {
        return $this->userManager->findUserBy([
            'externalId' => $externalId,
        ]);
    }
}
