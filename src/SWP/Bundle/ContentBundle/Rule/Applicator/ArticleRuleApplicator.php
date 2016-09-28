<?php

/**
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

use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArticleRuleApplicator implements RuleApplicatorInterface
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $supportedKeys = ['route', 'templateName', 'published'];

    /**
     * ArticleRuleApplicator constructor.
     *
     * @param RouteProviderInterface $routeProvider
     * @param LoggerInterface        $logger
     */
    public function __construct(RouteProviderInterface $routeProvider, LoggerInterface $logger)
    {
        $this->routeProvider = $routeProvider;
        $this->logger = $logger;
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
        if (isset($configuration[$this->supportedKeys[0]])) {
            $route = $this->routeProvider->getOneById($configuration[$this->supportedKeys[0]]);

            if (null === $route) {
                $this->logger->warning('Route not found! Make sure the rule defines an existing route!');

                return;
            }

            $subject->setRoute($route);
        }

        $subject->setTemplateName($configuration[$this->supportedKeys[1]]);
        $subject->setPublishable((bool) $configuration[$this->supportedKeys[2]]);
        $subject->setPublishedAt(new \DateTime());

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
        $this->configureOptions($resolver);

        try {
            return $resolver->resolve($configuration);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return [];
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            $this->supportedKeys[1] => null,
            $this->supportedKeys[2] => false
        ]);
        $resolver->setDefined($this->supportedKeys[0]);
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
