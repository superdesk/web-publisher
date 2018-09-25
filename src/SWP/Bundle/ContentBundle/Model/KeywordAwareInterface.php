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

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;

interface KeywordAwareInterface
{
    public function getKeywords(): Collection;

    public function setKeywords(Collection $keywords): void;

    public function addKeyword(KeywordInterface $keyword): void;

    public function removeKeyword(KeywordInterface $keyword): void;

    public function hasKeyword(KeywordInterface $keyword): bool;

    public function getKeywordsNames(): array;
}
