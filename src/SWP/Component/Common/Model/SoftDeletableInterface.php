<?php

/**
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Common\Model;

/**
 * SoftDeletableInterface should be implemented by classes which needs to be
 * identified as deletable.
 */
interface SoftDeletableInterface
{
    /**
     * Check whether the object is deleted or not.
     *
     * @return bool Returns true if deleted else false
     */
    public function isDeleted();

    /**
     * Gets the deleted at datetime.
     *
     * @return \DateTime The DateTime instance
     */
    public function getDeletedAt();

    /**
     * Sets the deleted at datetime.
     *
     * @param \DateTime $deletedAt The DateTime instance
     */
    public function setDeletedAt(\DateTime $deletedAt);
}
