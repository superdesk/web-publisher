<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;

final class PackageStatusListener
{
    public function onArticleUnpublish(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        $package = $this->getPackage($article);

        if (ContentInterface::STATUS_USABLE !== $package->getPubStatus()) {
            return;
        }

        // TODO: think about situation when just one from few articles is unpublished.
        $package->setStatus(ContentInterface::STATUS_UNPUBLISHED);
    }

    public function onArticlePublish(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        $package = $this->getPackage($article);
        if (ContentInterface::STATUS_USABLE !== $package->getPubStatus()) {
            return;
        }

        $package->setStatus(ContentInterface::STATUS_PUBLISHED);
    }

    public function onArticleCancel(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        $package = $this->getPackage($article);

        if (ContentInterface::STATUS_CANCELED !== $package->getPubStatus()) {
            return;
        }

        $package->setStatus(ContentInterface::STATUS_CANCELED);
    }

    /**
     * @return PackageInterface
     */
    private function getPackage(ArticleInterface $article)
    {
        /** @var PackageInterface $package */
        if (!($package = $article->getPackage()) instanceof PackageInterface) {
            throw UnexpectedTypeException::unexpectedType(is_object($package) ? get_class($package) : gettype($package), PackageInterface::class);
        }

        return $package;
    }
}
