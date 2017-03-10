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

namespace SWP\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\CoreBundle\Model\UserInterface;

interface ApiKeyRepositoryInterface
{
    /**
     * @param string $token
     *
     * @return QueryBuilder
     */
    public function getValidToken(string $token): QueryBuilder;

    /**
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function getValidTokenForUser(UserInterface $user);
}
