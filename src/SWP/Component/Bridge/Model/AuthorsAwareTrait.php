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

trait AuthorsAwareTrait
{
    protected $authors;

    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function setAuthors(Collection $authors): void
    {
        $this->authors = $authors;
    }

    public function addAuthor(AuthorInterface $author): void
    {
        if (!$this->hasAuthor($author)) {
            $this->authors->add($author);
        }
    }

    public function removeAuthor(AuthorInterface $author): void
    {
        if ($this->hasAuthor($author)) {
            $this->authors->removeElement($author);
        }
    }

    public function hasAuthor(AuthorInterface $author): bool
    {
        return $this->authors->contains($author);
    }

    public function getAuthorsNames()
    {
        return array_map(function ($author) { return $author->getName(); }, $this->authors->toArray());
    }
}
