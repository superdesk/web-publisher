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
use SWP\Bundle\ContentListBundle\Event\ContentListEvent;
use SWP\Bundle\CoreBundle\EventListener\AutomaticListAddArticleListener;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Matcher\ArticleCriteriaMatcherInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
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
        ArticleCriteriaMatcherInterface $articleCriteriaMatcher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith(
            $listRepository,
            $listItemFactory,
            $articleCriteriaMatcher,
            $eventDispatcher
        );
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
        ArticleCriteriaMatcherInterface $articleCriteriaMatcher,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $contentListItem,
        EventDispatcherInterface $eventDispatcher
    ) {
        $event->getArticle()->willReturn($article);

        $list->getFilters()->willReturn(['metadata' => ['locale' => 'en']]);
        $list->getItems()->willReturn(new ArrayCollection());
        $listRepository->findAll()->willReturn([$list]);

        $articleCriteriaMatcher->match($article, new Criteria(['metadata' => ['locale' => 'en']]))->willReturn(true);

        $listItemFactory->create()->willReturn($contentListItem);

        $contentListItem->setContent($article)->shouldBeCalled();
        $contentListItem->setPosition(0)->shouldBeCalled();

        $list->addItem($contentListItem)->shouldBeCalled();

        $this->addArticleToList($event);

        $eventDispatcher->dispatch(
            ContentListEvents::POST_ITEM_ADD,
            Argument::type(ContentListEvent::class)
        )->shouldHaveBeenCalled();
    }

    public function it_should_not_add_article_to_list(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListInterface $list,
        ArticleCriteriaMatcherInterface $articleCriteriaMatcher,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $contentListItem,
        EventDispatcherInterface $eventDispatcher
    ) {
        $event->getArticle()->willReturn($article);

        $list->getFilters()->willReturn(['metadata' => ['locale' => 'en']]);
        $list->getItems()->willReturn(new ArrayCollection());
        $listRepository->findAll()->willReturn([$list]);

        $articleCriteriaMatcher->match($article, new Criteria(['metadata' => ['locale' => 'en']]))->willReturn(false);

        $listItemFactory->create()->willReturn($contentListItem)->shouldNotBeCalled();

        $contentListItem->setContent($article)->shouldNotBeCalled();
        $contentListItem->setPosition(0)->shouldNotBeCalled();

        $list->addItem($contentListItem)->shouldNotBeCalled();

        $this->addArticleToList($event);

        $eventDispatcher->dispatch(
            ContentListEvents::POST_ITEM_ADD,
            Argument::type(ContentListEvent::class)
        )->shouldNotHaveBeenCalled();
    }
}
