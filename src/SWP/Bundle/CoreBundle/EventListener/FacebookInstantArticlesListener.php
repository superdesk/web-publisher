<?php

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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;

class FacebookInstantArticlesListener
{
    public function __construct($templateParser, MetaFactory $metaFactory)
    {
        $this->templateParser = $templateParser;
        $this->metaFactory = $metaFactory;
    }

    /**
     * @param ArticleEvent $event
     */
    public function sendArticleToFacebook(ArticleEvent $event)
    {
        $this->metaFactory->create($event->getArticle());
        $instantArticle = $this->templateParser->parse();

        dump($instantArticle);
        die;
    }
}
