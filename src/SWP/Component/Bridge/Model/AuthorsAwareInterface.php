<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
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

interface AuthorsAwareInterface
{
    public function getAuthors(): Collection;

    public function setAuthors(Collection $authors): void;

    public function addAuthor(AuthorInterface $author): void;

    public function removeAuthor(AuthorInterface $author): void;

    public function hasAuthor(AuthorInterface $author): bool;
}
