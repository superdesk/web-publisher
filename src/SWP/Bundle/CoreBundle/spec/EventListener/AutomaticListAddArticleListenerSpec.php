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

namespace spec\SWP\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\EventListener\AutomaticListAddArticleListener;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin AutomaticListAddArticleListener
 */
final class AutomaticListAddArticleListenerSpec extends ObjectBehavior
{
    public function let(
        ContentListRepositoryInterface $listRepository,
        FactoryInterface $listItemFactory,
        RuleEvaluatorInterface $ruleEvaluator,
        FactoryInterface $ruleFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($listRepository, $listItemFactory, $ruleEvaluator, $ruleFactory, $eventDispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AutomaticListAddArticleListener::class);
    }

    public function it_adds_article_to_list(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListInterface $list,
        FactoryInterface $ruleFactory,
        RuleInterface $rule,
        RuleEvaluatorInterface $ruleEvaluator,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $listItem
    ) {
        $event->getArticle()->willReturn($article);

        $list->getExpression()->willReturn('article.getLocale() == "en"');
        $list->getItems()->willReturn(new ArrayCollection());
        $listRepository->findByType(ContentListInterface::TYPE_AUTOMATIC)->willReturn([$list]);

        $ruleFactory->create()->willReturn($rule);

        $rule->setExpression('article.getLocale() == "en"')->shouldBeCalled();

        $ruleEvaluator->evaluate($rule, $article)->willReturn(true);
        $listItemFactory->create()->willReturn($listItem);

        $listItem->setContent($article)->shouldBeCalled();
        $listItem->setPosition(Argument::type('integer'))->shouldBeCalled();

        $list->addItem($listItem)->shouldBeCalled();

        $this->addArticleToList($event);
    }

    public function it_should_not_add_article_to_list(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListInterface $list,
        FactoryInterface $ruleFactory,
        RuleInterface $rule,
        RuleEvaluatorInterface $ruleEvaluator,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $listItem
    ) {
        $event->getArticle()->willReturn($article);

        $list->getExpression()->willReturn('article.getLocale() == "en"');
        $listRepository->findByType(ContentListInterface::TYPE_AUTOMATIC)->willReturn([$list]);

        $ruleFactory->create()->willReturn($rule);

        $rule->setExpression('article.getLocale() == "en"')->shouldBeCalled();

        $ruleEvaluator->evaluate($rule, $article)->willReturn(false);
        $listItemFactory->create()->shouldNotBeCalled();

        $listItem->setContent($article)->shouldNotBeCalled();

        $list->addItem($listItem)->shouldNotBeCalled();

        $this->addArticleToList($event);
    }
}
