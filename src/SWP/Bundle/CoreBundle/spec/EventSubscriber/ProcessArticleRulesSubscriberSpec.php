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

namespace spec\SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\EventSubscriber\ProcessArticleRulesSubscriber;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @mixin ProcessArticleRulesSubscriber
 */
final class ProcessArticleRulesSubscriberSpec extends ObjectBehavior
{
    public function let(RuleProcessorInterface $ruleProcessor, \SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface $publishDestinationProvider)
    {
        $this->beConstructedWith($ruleProcessor, $publishDestinationProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ProcessArticleRulesSubscriber::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function it_subscribes_to_events()
    {
        $this->getSubscribedEvents()->shouldReturn([
            ArticleEvents::POST_CREATE => 'processRules',
            ArticleEvents::POST_UPDATE => 'processRules',
        ]);
    }

    public function it_processes_rules(
        ArticleEvent $event,
        ArticleInterface $article,
        \SWP\Bundle\CoreBundle\Model\PackageInterface $package,
        RuleProcessorInterface $ruleProcessor,
        \SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface $publishDestinationProvider
    ) {
        $event->getArticle()->willReturn($article);
        $event->getPackage()->willReturn($package);
        $publishDestinationProvider->countDestinations($package)->willReturn(0);
        $ruleProcessor->process($article)->shouldBeCalled();

        $this->processRules($event);
    }
}
