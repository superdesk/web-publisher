<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú`
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface ServiceInterface extends PersistableInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getMetadata(): ?MetadataInterface;

    public function setMetadata(?MetadataInterface $metadata): void;
}
