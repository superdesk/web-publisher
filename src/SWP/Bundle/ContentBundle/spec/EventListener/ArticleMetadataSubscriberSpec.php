<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\EventListener\ArticleMetadataSubscriber;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @mixin ArticleMetadataSubscriber
 */
class ArticleMetadataSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleMetadataSubscriber::class);
    }

    function it_is_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            ArticleEvents::PRE_CREATE => 'populateMetadata',
        ]);
    }

    function it_updates_article_metadata(
        ArticleEvent $event,
        ArticleInterface $article,
        PackageInterface $package
    ) {
        $event->getArticle()->willReturn($article);
        $event->getPackage()->willReturn($package);

        $package->getUrgency()->shouldBeCalled();
        $package->getPriority()->shouldBeCalled();
        $package->getLocated()->shouldBeCalled();
        $package->getPlaces()->shouldBeCalled();
        $package->getServices()->shouldBeCalled();
        $package->getSubjects()->shouldBeCalled();
        $package->getType()->shouldBeCalled();
        $package->getByLine()->shouldBeCalled();
        $package->getGuid()->shouldBeCalled();
        $package->getEdNote()->shouldBeCalled();
        $package->getGenre()->shouldBeCalled();
        $package->getLanguage()->shouldBeCalled();
        $package->getCreatedAt()->shouldBeCalled();

        $article->setMetadata(Argument::type('array'))->shouldBeCalled();

        $this->populateMetadata($event);
    }

    function it_should_throw_exception(ArticleEvent $event)
    {
        $event->getPackage()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringPopulateMetadata($event);
    }
}
