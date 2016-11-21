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

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessArticleRulesSubscriber implements EventSubscriberInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $ruleProcessor;

    /**
     * ProcessArticleRulesSubscriber constructor.
     *
     * @param RuleProcessorInterface $ruleProcessor
     */
    public function __construct(RuleProcessorInterface $ruleProcessor)
    {
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::PRE_CREATE => 'processRules',
        ];
    }

    /**
     * @param ArticleEvent $event
     */
    public function processRules(ArticleEvent $event)
    {
        $this->ruleProcessor->process($event->getArticle());
    }
}
