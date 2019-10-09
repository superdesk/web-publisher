<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface GroupInterface extends PersistableInterface, SoftDeletableInterface
{
    public const TYPE_RELATED = 'related_content';

    public function getCode(): string;

    public function setCode(string $code): void;

    public function getItems(): Collection;

    public function setItems(Collection $items): void;

    public function getPackage(): ?PackageInterface;

    public function setPackage(?PackageInterface $package): void;

    public function getType(): ?string;

    public function setType(?string $type): void;
}
