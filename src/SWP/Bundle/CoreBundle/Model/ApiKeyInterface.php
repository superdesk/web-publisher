<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ApiKeyInterface extends TimestampableInterface, PersistableInterface
{
    /**
     * @return string
     */
    public function getApiKey(): string;

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey);

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return \DateTime
     */
    public function getValidTo(): \DateTime;

    /**
     * @param \DateTime $validTo
     */
    public function setValidTo(\DateTime $validTo);

    /**
     *  Extend token.
     */
    public function extendValidTo();
}
