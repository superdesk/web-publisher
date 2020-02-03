<?php

/*
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
 * SoftDeletableInterface should be implemented by classes which needs to be
 * identified as deletable.
 */
interface SoftDeletableInterface
{
    public const SOFT_DELETEABLE_FILTER_NAME = 'soft_deleteable';

    public function isDeleted(): bool;

    public function getDeletedAt(): ?\DateTimeInterface;

    public function setDeletedAt(\DateTime $deletedAt): void;

    public function restore(): void;
}
