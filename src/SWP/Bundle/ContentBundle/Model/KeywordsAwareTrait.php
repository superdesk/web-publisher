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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait KeywordsAwareTrait
{
    protected $keywords;

    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function setKeywords(Collection $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function addKeyword(KeywordInterface $keyword): void
    {
        if (!$this->hasKeyword($keyword)) {
            $this->keywords->add($keyword);
        }
    }

    public function removeKeyword(KeywordInterface $keyword): void
    {
        if ($this->hasKeyword($keyword)) {
            $this->keywords->removeElement($keyword);
        }

        // Reset internal array keys, so it's serialized to json as array not object
        $this->setKeywords(new ArrayCollection($this->getKeywords()->getValues()));
    }

    public function hasKeyword(KeywordInterface $keyword): bool
    {
        return $this->keywords->contains($keyword);
    }

    public function getKeywordsNames(): array
    {
        return array_map(function ($keyword) {
            return $keyword->getName();
        }, $this->keywords->toArray());
    }
}
