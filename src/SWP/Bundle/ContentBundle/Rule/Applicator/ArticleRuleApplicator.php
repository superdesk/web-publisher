<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Rule\Applicator;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Rule\Applicator\AbstractRuleApplicator;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArticleRuleApplicator extends AbstractRuleApplicator
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $supportedKeys = ['route', 'templateName', 'published'];

    /**
     * ArticleRuleApplicator constructor.
     *
     * @param RouteProviderInterface   $routeProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RouteProviderInterface $routeProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->routeProvider = $routeProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $configuration = $this->validateRuleConfiguration($rule->getConfiguration());
        if (!$this->isAllowedType($subject) || empty($configuration)) {
            return;
        }

        /* @var ArticleInterface $subject */
        if (isset($configuration['route'])) {
            $route = $this->routeProvider->getOneById($configuration['route']);

            if (null === $route) {
                $this->logger->warning('Route not found! Make sure the rule defines an existing route!');

                return;
            }

            $subject->setRoute($route);

            if (RouteInterface::TYPE_CONTENT === $route->getType()) {
                $route->setContent($subject);
            }
        }

        $subject->setTemplateName($configuration['templateName']);

        if ((bool) $configuration['published']) {
            $this->eventDispatcher->dispatch(new ArticleEvent($subject, null, ArticleEvents::PUBLISH), ArticleEvents::PUBLISH);
        }

        $this->logger->info(sprintf(
            'Configuration: "%s" for "%s" rule has been applied!',
            json_encode($configuration),
            $rule->getExpression()
        ));
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
            $this->supportedKeys[1] => null,
            $this->supportedKeys[2] => null,
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
