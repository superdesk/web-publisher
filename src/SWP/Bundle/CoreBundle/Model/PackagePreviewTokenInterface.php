<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteAwareInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface PackagePreviewTokenInterface extends PersistableInterface, TenantAwareInterface, TimestampableInterface, RouteAwareInterface
{
    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @param string $body
     */
    public function setBody(string $body): void;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param string $token
     */
    public function setToken(string $token): void;
}
