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
use SWP\Bundle\CoreBundle\ContentListEvents;
use SWP\Bundle\CoreBundle\Event\ContentListEvent;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AutomaticListAddArticleListener
{
    /**
     * @var ContentListRepositoryInterface
     */
    private $listRepository;

    /**
     * @var FactoryInterface
     */
    private $listItemFactory;

    /**
     * @var RuleEvaluatorInterface
     */
    private $ruleEvaluator;

    /**
     * @var FactoryInterface
     */
    private $ruleFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * AutomaticListAddArticleListener constructor.
     *
     * @param ContentListRepositoryInterface $listRepository
     * @param FactoryInterface               $listItemFactory
     * @param RuleEvaluatorInterface         $ruleEvaluator
     * @param FactoryInterface               $ruleFactory
     * @param EventDispatcherInterface       $eventDispatcher
     */
    public function __construct(
        ContentListRepositoryInterface $listRepository,
        FactoryInterface $listItemFactory,
        RuleEvaluatorInterface $ruleEvaluator,
        FactoryInterface $ruleFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->listRepository = $listRepository;
        $this->listItemFactory = $listItemFactory;
        $this->ruleEvaluator = $ruleEvaluator;
        $this->ruleFactory = $ruleFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ArticleEvent $event
     */
    public function addArticleToList(ArticleEvent $event)
    {
        $article = $event->getArticle();
        /** @var ContentListInterface[] $contentLists */
        $contentLists = $this->listRepository->findByType(ContentListInterface::TYPE_AUTOMATIC);
        /** @var RuleInterface $rule */
        $rule = $this->ruleFactory->create();

        foreach ($contentLists as $contentList) {
            $rule->setExpression($contentList->getExpression());
            if ($this->ruleEvaluator->evaluate($rule, $article)) {
                /** @var ContentListItemInterface $contentListItem */
                $contentListItem = $this->listItemFactory->create();
                $contentListItem->setContent($article);
                $contentListItem->setPosition($contentList->getItems()->count());
                $contentList->addItem($contentListItem);
                $this->eventDispatcher->dispatch(ContentListEvents::POST_ITEM_ADD, new ContentListEvent($contentList, $contentListItem));
            }
        }
    }
}
