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
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessArticleRulesSubscriber implements EventSubscriberInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $ruleProcessor;

    /**
     * @var PublishDestinationProviderInterface
     */
    private $publishDestinationProvider;

    /**
     * ProcessArticleRulesSubscriber constructor.
     *
     * @param RuleProcessorInterface              $ruleProcessor
     * @param PublishDestinationProviderInterface $publishDestinationProvider
     */
    public function __construct(
        RuleProcessorInterface $ruleProcessor,
        PublishDestinationProviderInterface $publishDestinationProvider
    ) {
        $this->ruleProcessor = $ruleProcessor;
        $this->publishDestinationProvider = $publishDestinationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::POST_CREATE => 'processRules',
            ArticleEvents::POST_UPDATE => 'processRules',
        ];
    }

    /**
     * @param ArticleEvent $event
     */
    public function processRules(ArticleEvent $event)
    {
        /** @var PackageInterface $package */
        $package = $event->getPackage();
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        $count = $this->publishDestinationProvider->countDestinations($package);

        if (0 < $count) {
            return;
        }

        $this->ruleProcessor->process($article);
    }
}
