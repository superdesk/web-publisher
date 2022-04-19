<?php

declare(strict_types=1);

namespace spec\SWP\Bundle\CoreBundle\Rule\Applicator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\RuleInterface;
use SWP\Bundle\CoreBundle\Rule\Applicator\PublishArticleToFBIARuleApplicator;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class PublishArticleToFBIARuleApplicatorSpec extends ObjectBehavior
{
    public function let(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->beConstructedWith($eventDispatcher);
        $this->setLogger($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PublishArticleToFBIARuleApplicator::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleApplicatorInterface::class);
    }

    public function it_supports_articles(ArticleInterface $subject)
    {
        $subject->getSubjectType()->willReturn('article');

        $this->isSupported($subject)->shouldReturn(true);
    }

    public function it_doesn_not_support_when_type_is_wrong(ArticleInterface $subject)
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
            'fake' => 'fake',
        ]);

        $logger->warning(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_should_return_when_no_configuration(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $rule->getConfiguration()->willReturn([]);

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_applies_rule(
        RuleInterface $rule,
        ArticleInterface $subject,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $rule->getConfiguration()->willReturn([
            'isPublishedFbia' => true,
        ]);
        $rule->getExpression()->willReturn('article.getSomething("something") matches /something/');

        $subject->setPublishedFBIA(true)->shouldBeCalled();
        $eventDispatcher->dispatch( Argument::type(ArticleEvent::class), ArticleEvents::PUBLISH)->shouldBeCalled();
        $logger->info(Argument::any('string'))->shouldBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }

    public function it_processes_but_doesnt_apply_rule(
        RuleInterface $rule,
        ArticleInterface $subject,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $rule->getConfiguration()->willReturn([
            'isPublishedFbia' => false,
        ]);
        $rule->getExpression()->willReturn('article.getSomething("something") matches /something/');

        $subject->setPublishedFBIA(true)->shouldNotBeCalled();
        $eventDispatcher->dispatch( Argument::type(ArticleEvent::class), ArticleEvents::PUBLISH)->shouldNotBeCalled();
        $logger->info(Argument::any('string'))->shouldNotBeCalled();

        $this->apply($rule, $subject)->shouldReturn(null);
    }
}
