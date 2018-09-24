<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface KeywordInterface extends PersistableInterface
{
    public function getSlug(): string;

    public function setSlug(string $slug): void;

    public function getName(): string;

    public function setName(string $name): void;
}
