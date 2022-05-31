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

namespace SWP\Bundle\ContentBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Processor\ArticleAuthorProcessor;

final class PopulateArticleAuthorSlugListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof ArticleAuthorInterface) {
            return;
        }

        ArticleAuthorProcessor::setSlugInArticleAuthor($object);
    }
}
