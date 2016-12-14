<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\EventListener;

use SWP\Bundle\ContentListBundle\Remover\ContentListItemsRemoverInterface;
use SWP\Component\ContentList\Model\ContentListInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class RemoveItemsListener
{
    private $contentListItemsRemover;

    public function __construct(ContentListItemsRemoverInterface $contentListItemsRemover)
    {
        $this->contentListItemsRemover = $contentListItemsRemover;
    }

    public function onListCriteriaChange(GenericEvent $event)
    {
        $contentList = $event->getSubject();
        if (!$contentList instanceof ContentListInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "%s", "%s" given.',
                ContentListInterface::class,
                is_object($contentList) ? get_class($contentList) : gettype($contentList))
            );
        }

        if ($contentList->getExpression() !== $event->getArgument('expression')) {
            $this->contentListItemsRemover->removeContentListItems($contentList);
            // transform expression from string to Criteria so it can be used in query builder
            // to fetch articles which need to be added to the list

            // add articles to list matching new criteria
        }
    }
}
