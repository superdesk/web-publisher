<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Matcher;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Criteria\Criteria;

final class ArticleCriteriaMatcher implements ArticleCriteriaMatcherInterface
{
    public function match(ArticleInterface $article, Criteria $criteria)
    {
        if (0 === $criteria->count()) {
            return false;
        }

        if ($criteria->has('route')) {
            foreach ($criteria->get('route') as $value) {
                if (null !== $article->getRoute() && (int) $value !== $article->getRoute()->getId()) {
                    return false;
                }
            }
        }

        if ($criteria->has('author') && is_array($criteria->get('author'))) {
            foreach ($criteria->get('author') as $key => $value) {
                if ($value !== $article->getMetadataByKey('byline')) {
                    return false;
                }
            }
        }

        if ($criteria->has('publishedBefore')) {
            $publishedBefore = new \DateTime($criteria->get('publishedBefore'));
            if ($article->getPublishedAt() > $publishedBefore) {
                return false;
            }
        }

        if ($criteria->has('publishedAfter')) {
            $publishedAfter = new \DateTime($criteria->get('publishedAfter'));
            if ($article->getPublishedAt() < $publishedAfter) {
                return false;
            }
        }

        if ($criteria->has('publishedAt')
            && (!$criteria->has('publishedBefore') || !$criteria->has('publishedAfter'))) {
            $publishedAt = new \DateTime($criteria->get('publishedAt'));
            if ($publishedAt->format('d-m-Y') !== $article->getPublishedAt()->format('d-m-Y')) {
                return false;
            }
        }

        if ($criteria->has('metadata') && !empty($criteria->get('metadata'))) {
            $criteriaMetadata = $criteria->get('metadata');

            if (!is_array($criteriaMetadata)) {
                return false;
            }

            if ($this->isArticleMetadataMatchingCriteria($criteriaMetadata, $article)) {
                return true;
            }
        }

        return false;
    }

    private function isArticleMetadataMatchingCriteria(array $criteriaMetadata, ArticleInterface $article): bool
    {
        foreach ($criteriaMetadata as $key => $criteriaMetadataItem) {
            $articleMetadataByKey = $article->getMetadataByKey($key);

            if (is_array($articleMetadataByKey)) {
                foreach ($articleMetadataByKey as $articleMetadataItem) {
                    if ($this->isMetadataMatching($articleMetadataItem, $criteriaMetadataItem)) {
                        return true;
                    }
                }
            } elseif ($criteriaMetadataItem === $articleMetadataByKey) {
                return true;
            }
        }

        return false;
    }

    private function isMetadataMatching(array $articleMetadataItem, array $criteriaMetadata): bool
    {
        foreach ($criteriaMetadata as $criteriaMetadataItem) {
            if (!isset($articleMetadataItem['code'], $criteriaMetadataItem['code'])) {
                continue;
            }

            if ($this->isSubjectMatching($articleMetadataItem, $criteriaMetadataItem)) {
                return true;
            }

            if ($articleMetadataItem['code'] === $criteriaMetadataItem['code']) {
                return true;
            }
        }

        return false;
    }

    private function isSubjectMatching(array $articleMetadataItem, array $criteriaMetadataItem): bool
    {
        return isset($articleMetadataItem['scheme'], $criteriaMetadataItem['scheme']) &&
            $articleMetadataItem['code'] === $criteriaMetadataItem['code'] &&
            $articleMetadataItem['scheme'] === $criteriaMetadataItem['scheme']
        ;
    }
}
