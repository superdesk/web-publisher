<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface ApplicationInterface extends PersistableInterface
{
    /**
     * @return string
     */
    public function getAppId();

    /**
     * @return string
     */
    public function getAppSecret();

    /**
     * @param string $appId
     */
    public function setAppId(string $appId);

    /**
     * @param string $appSecret
     */
    public function setAppSecret(string $appSecret);
}
