<?php

/*
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

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\EventListener\ProcessArticleRulesSubscriber;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @mixin ProcessArticleRulesSubscriber
 */
final class ProcessArticleRulesSubscriberSpec extends ObjectBehavior
{
    function let(RuleProcessorInterface $ruleProcessor)
    {
        $this->beConstructedWith($ruleProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProcessArticleRulesSubscriber::class);
    }

    function it_implements_an_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_events()
    {
        $this->getSubscribedEvents()->shouldReturn([
            ArticleEvents::PRE_CREATE => 'processRules',
        ]);
    }

    function it_processes_rules(
        ArticleEvent $event,
        ArticleInterface $article,
        RuleProcessorInterface $ruleProcessor
    ) {
        $event->getArticle()->willReturn($article);
        $ruleProcessor->process($article)->shouldBeCalled();

        $this->processRules($event);
    }
}
