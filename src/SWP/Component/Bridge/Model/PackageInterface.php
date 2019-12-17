<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;

interface PackageInterface extends ContentInterface, TimestampableInterface, EnableableInterface, SoftDeletableInterface
{
    public function getItems(): Collection;

    public function setItems(Collection $items);

    public function getExternalData(): ?Collection;

    public function setExternalData(Collection $externalData): void;

    public function getGroups(): ?Collection;

    public function setGroups(?Collection $groups): void;

    public function getRelatedItems(): Collection;

    public function setRelatedItems(Collection $relatedItems): void;
}
