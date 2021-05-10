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

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentListBundle\Event\ContentListEvent;
use SWP\Bundle\ContentListBundle\Services\ContentListServiceInterface;
use SWP\Bundle\CoreBundle\EventListener\AddArticleToListListener;
use SWP\Bundle\CoreBundle\Matcher\ArticleCriteriaMatcherInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AddArticleToListListenerSpec extends ObjectBehavior
{
    public function let(
        ContentListRepositoryInterface $listRepository,
        FactoryInterface $listItemFactory,
        ArticleCriteriaMatcherInterface $articleCriteriaMatcher,
        EventDispatcherInterface $eventDispatcher,
        ContentListItemRepositoryInterface $listItemRepository,
        ContentListServiceInterface $contentListService,
        EntityManagerInterface $entityManager
    ) {
        $this->beConstructedWith(
            $listRepository,
            $listItemFactory,
            $articleCriteriaMatcher,
            $eventDispatcher,
            $listItemRepository,
            $contentListService,
            $entityManager
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AddArticleToListListener::class);
    }

    public function it_adds_article_to_list(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListItemRepositoryInterface $listItemRepository,
        ContentListInterface $list,
        ArticleCriteriaMatcherInterface $articleCriteriaMatcher,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $contentListItem,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager
    ) {
        $event->getArticle()->willReturn($article);

        $list->getFilters()->willReturn(['metadata' => ['locale' => 'en']]);
        $listRepository->findByTypes(['automatic'])->willReturn([$list]);
        $listItemRepository->persist(Argument::any())->shouldBeCalled();
        $listItemRepository->flush()->shouldBeCalled();
        $listItemRepository->findItemByArticleAndList(
            Argument::type(ArticleInterface::class),
            Argument::type(ContentListInterface::class),
            'automatic'
        )->willReturn(null);

        $articleCriteriaMatcher->match($article, new Criteria(['metadata' => ['locale' => 'en']]))->willReturn(true);

        $listItemFactory->create()->willReturn($contentListItem);

        $contentListItem->setContent($article)->shouldBeCalled();
        $contentListItem->setPosition(0)->shouldBeCalled();
        $contentListItem->setContentList($list)->shouldBeCalled();

        $list->setUpdatedAt(Argument::type(\DateTime::class))->shouldBeCalled();

        $this->addArticleToList($event);

        $eventDispatcher->dispatch(
            Argument::type(ContentListEvent::class),
            ContentListEvents::POST_ITEM_ADD
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
        $listRepository->findByTypes(['automatic'])->willReturn([$list]);

        $articleCriteriaMatcher->match($article, new Criteria(['metadata' => ['locale' => 'en']]))->willReturn(false);

        $listItemFactory->create()->willReturn($contentListItem)->shouldNotBeCalled();

        $contentListItem->setContent($article)->shouldNotBeCalled();
        $contentListItem->setPosition(-1)->shouldNotBeCalled();

        $this->addArticleToList($event);

        $eventDispatcher->dispatch(
            Argument::type(ContentListEvent::class),
            ContentListEvents::POST_ITEM_ADD
        )->shouldNotHaveBeenCalled();
    }

    public function it_adds_article_to_bucket(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListInterface $list,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $contentListItem,
        EventDispatcherInterface $eventDispatcher
    ) {
        $article->isPublishedFBIA()->willReturn(true);
        $event->getArticle()->willReturn($article);
        $listRepository->findByTypes(['bucket'])->willReturn([$list]);
        $listItemFactory->create()->willReturn($contentListItem);
        $contentListItem->setContent($article)->shouldBeCalled();
        $contentListItem->setPosition(0)->shouldBeCalled();
        $contentListItem->setContentList($list)->shouldBeCalled();
        $list->setUpdatedAt(Argument::type(\DateTime::class))->shouldBeCalled();

        $this->addArticleToBucket($event);

        $eventDispatcher->dispatch(
            Argument::type(ContentListEvent::class),
            ContentListEvents::POST_ITEM_ADD
        )->shouldHaveBeenCalled();
    }

    public function it_shouldnt_add_article_to_bucket(
        ArticleEvent $event,
        Article $article,
        ContentListRepositoryInterface $listRepository,
        ContentListInterface $list,
        FactoryInterface $listItemFactory,
        ContentListItemInterface $contentListItem,
        EventDispatcherInterface $eventDispatcher
    ) {
        $article->isPublishedFBIA()->willReturn(false);
        $event->getArticle()->willReturn($article);
        $listRepository->findByTypes(['bucket'])->willReturn([$list]);
        $listItemFactory->create()->willReturn($contentListItem)->shouldNotBeCalled();
        $contentListItem->setContent($article)->shouldNotBeCalled();
        $contentListItem->setPosition(-1)->shouldNotBeCalled();

        $this->addArticleToBucket($event);

        $eventDispatcher->dispatch(
            Argument::type(ContentListEvent::class),
            ContentListEvents::POST_ITEM_ADD
        )->shouldNotHaveBeenCalled();
    }
}
