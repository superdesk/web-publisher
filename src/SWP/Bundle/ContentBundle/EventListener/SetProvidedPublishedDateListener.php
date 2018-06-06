<?php

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

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;

class SetProvidedPublishedDateListener
{
    private const DATE_FORMAT = 'Y-m-d\TH:i:sO';

    /**
     * @param ArticleEvent $event
     */
    public function onArticleCreate(ArticleEvent $event)
    {
        $article = $event->getArticle();
        $extra = $article->getExtra();

        if (isset($extra['original_published_at']) && $this->validateDate($extra['original_published_at'])) {
            $publishedDate = \DateTime::createFromFormat(self::DATE_FORMAT, $extra['original_published_at']);
            $publishedDate->setTimezone(new \DateTimeZone('UTC'));
            $article->setPublishedAt($publishedDate);
        }
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    private function validateDate($date)
    {
        $dateTime = \DateTime::createFromFormat(self::DATE_FORMAT, $date);
        if ($dateTime && $dateTime->format(self::DATE_FORMAT) === $date) {
            return true;
        }

        $dateTime = \DateTime::createFromFormat(\DateTime::ATOM, $date);
        if ($dateTime && $dateTime->format(\DateTime::ATOM) === $date) {
            return true;
        }

        return false;
    }
}
