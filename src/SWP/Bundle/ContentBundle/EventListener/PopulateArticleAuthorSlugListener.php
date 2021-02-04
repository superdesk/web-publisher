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

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Processor\ArticleAuthorProcessor;
use SWP\Component\Common\Model\TimestampableInterface;

final class PopulateArticleAuthorSlugListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();

        // HACK: FIX: for not set created at
        if ($object instanceof TimestampableInterface && null === $object->getCreatedAt()) {
            //$object->setCreatedAt(new \DateTime());
        }

        if (!$object instanceof ArticleAuthorInterface) {
            return;
        }

        ArticleAuthorProcessor::setSlugInArticleAuthor($object);
    }
}
