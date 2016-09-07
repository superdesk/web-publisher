<?php

/**
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Model;

/**
 * TimestampableInterface should be implemented by classes which needs to be
 * identified as Timestampable.
 */
interface TimestampableInterface
{
    /**
     * Gets the created at datetime.
     *
     * @return \DateTime The DateTime instance
     */
    public function getCreatedAt();

    /**
     * Sets the created at datetime.
     *
     * @param \DateTime $createdAt The DateTime instance
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Gets the updated at datetime.
     *
     * @return \DateTime The DateTime instance
     */
    public function getUpdatedAt();

    /**
     * Sets the updated at datetime.
     *
     * @param \DateTime $updatedAt The DateTime instance
     */
    public function setUpdatedAt(\DateTime $updatedAt);
}
