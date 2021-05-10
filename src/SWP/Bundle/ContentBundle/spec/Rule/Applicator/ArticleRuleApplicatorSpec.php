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

namespace spec\SWP\Bundle\ContentBundle\Rule\Applicator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\Rule\Applicator\ArticleRuleApplicator;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin ArticleRuleApplicator
 */
final class ArticleRuleApplicatorSpec extends ObjectBehavior
{
    public function let(
        RouteProviderInterface $routeProvider,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->beConstructedWith($routeProvider, $eventDispatcher);
        $this->setLogger($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleRuleApplicator::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleApplicatorInterface::class);
    }

    public function it_supports_articles(Article $subject)
    {
        $subject->getSubjectType()->willReturn('article');

        $this->isSupported($subject)->shouldReturn(true);
    }

    public function it_doesn_not_support_when_type_is_wrong(Article $subject)
    {
        $subject->getSubjectType()->willReturn('fake');

        $this->isSupported($subject)->shouldReturn(false);
    }

    public function it_should_not_apply_rule_when_wrong_type(
        RuleInterface $rule,
        RuleSubjectInterface $subject,
        LoggerInterface $logger
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
        ]);

        $logger->warning(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_should_return_when_no_configuration(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $rule->getConfiguration()->willReturn([]);

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_should_not_apply_rule_when_route_not_found(
        RuleInterface $rule,
        Article $subject,
        LoggerInterface $logger
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
        ]);

        $logger->warning(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_applies_rule(
        RuleInterface $rule,
        Article $subject,
        RouteProviderInterface $routeProvider,
        RouteInterface $route,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
            'published' => 'true',
        ]);
        $rule->getExpression()->willReturn('article.getSomething("something") matches /something/');
        $routeProvider->getOneById('some/route')->willReturn($route);

        $subject->setRoute($route)->shouldBeCalled();
        $subject->setTemplateName('template.twig.html')->shouldBeCalled();
        $eventDispatcher->dispatch(Argument::type(ArticleEvent::class), ArticleEvents::PUBLISH)->shouldBeCalled();
        $logger->info(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_applies_rule_and_assigns_content_to_route(
        RuleInterface $rule,
        Article $subject,
        RouteProviderInterface $routeProvider,
        RouteInterface $route,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
            'published' => 'true',
        ]);
        $rule->getExpression()->willReturn('article.getSomething("something") matches /something/');
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $routeProvider->getOneById('some/route')->willReturn($route);

        $subject->setRoute($route)->shouldBeCalled();
        $route->setContent($subject)->shouldBeCalled();
        $subject->setTemplateName('template.twig.html')->shouldBeCalled();
        $eventDispatcher->dispatch(Argument::type(ArticleEvent::class), ArticleEvents::PUBLISH)->shouldBeCalled();
        $logger->info(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }
}
