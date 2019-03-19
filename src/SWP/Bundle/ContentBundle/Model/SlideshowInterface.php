<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SlideshowInterface extends PersistableInterface, TimestampableInterface, SoftDeletableInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getArticle(): ?ArticleInterface;

    public function setArticle(?ArticleInterface $article): void;

    public function getItems(): Collection;

    public function setItems(Collection $items): void;

    public function addItem(SlideshowItemInterface $item): void;

    public function hasItem(SlideshowItemInterface $item): bool;
}
