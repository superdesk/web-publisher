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
namespace spec\SWP\Bundle\ContentBundle\Rule\Applicator;

use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\Rule\Applicator\ArticleRuleApplicator;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;

/**
 * @mixin ArticleRuleApplicator
 */
final class ArticleRuleApplicatorSpec extends ObjectBehavior
{
    function  let(
        RouteProviderInterface $routeProvider,
        LoggerInterface $logger,
        ArticleServiceInterface $articleService
    ) {
        $this->beConstructedWith($routeProvider, $logger, $articleService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleRuleApplicator::class);
    }

    function it_implements_an_interface()
    {
        $this->shouldImplement(RuleApplicatorInterface::class);
    }

    function it_supports_articles(ArticleInterface $subject)
    {
        $subject->getSubjectType()->willReturn('article');

        $this->isSupported($subject)->shouldReturn(true);
    }

    function it_doesn_not_support_when_type_is_wrong(ArticleInterface $subject)
    {
        $subject->getSubjectType()->willReturn('fake');

        $this->isSupported($subject)->shouldReturn(false);
    }

    function it_should_not_apply_rule_when_wrong_type(
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

    function it_should_return_when_no_configuration(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $rule->getConfiguration()->willReturn([]);

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    function it_should_not_apply_rule_when_route_not_found(
        RuleInterface $rule,
        ArticleInterface $subject,
        LoggerInterface $logger
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
        ]);

        $logger->warning(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    function it_applies_rule(
        RuleInterface $rule,
        ArticleInterface $subject,
        RouteProviderInterface $routeProvider,
        RouteInterface $route,
        LoggerInterface $logger,
        ArticleServiceInterface $articleService
    ) {
        $rule->getConfiguration()->willReturn([
            'route' => 'some/route',
            'templateName' => 'template.twig.html',
            'published' => 'true'
        ]);
        $rule->getExpression()->willReturn('article.getSomething("something") matches /something/');
        $routeProvider->getOneById('some/route')->willReturn($route);

        $subject->setRoute($route)->shouldBeCalled();
        $subject->setTemplateName('template.twig.html')->shouldBeCalled();
        $articleService->publish($subject)->shouldBeCalled();
        $logger->info(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }
}
