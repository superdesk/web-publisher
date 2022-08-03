<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Rule\Applicator;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Rule\Applicator\AbstractRuleApplicator;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublishArticleToAppleNewsRuleApplicator extends AbstractRuleApplicator
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $supportedKeys = ['isPublishedToAppleNews'];

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $configuration = $this->validateRuleConfiguration($rule->getConfiguration());

        if (empty($configuration) || !$this->isAllowedType($subject)) {
            return;
        }

        if ($isPublishedToAppleNews = (bool) $configuration[$this->supportedKeys[0]]) {
            $subject->setPublishedToAppleNews($isPublishedToAppleNews);
            $this->eventDispatcher->dispatch( new ArticleEvent($subject, null, ArticleEvents::PUBLISH), ArticleEvents::PUBLISH);

            $this->logger->info(sprintf(
                'Configuration: "%s" for "%s" rule has been applied!',
                json_encode($configuration, JSON_THROW_ON_ERROR, 512),
                $rule->getExpression()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(RuleSubjectInterface $subject)
    {
        return $subject instanceof ArticleInterface && 'article' === $subject->getSubjectType();
    }

    private function validateRuleConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $configuration);

        return $this->resolveConfig($resolver, $configuration);
    }

    private function configureOptions(OptionsResolver $resolver, array $configuration)
    {
        $resolver->setDefaults([
            $this->supportedKeys[0] => false,
        ]);
        $resolver->setDefined(array_keys($configuration));
    }

    private function isAllowedType(RuleSubjectInterface $subject)
    {
        if (!$subject instanceof ArticleInterface) {
            $this->logger->warning(sprintf(
                '"%s" is not supported by "%s" rule applicator!',
                is_object($subject) ? get_class($subject) : gettype($subject),
                get_class($this)
            ));

            return false;
        }

        return true;
    }
}
