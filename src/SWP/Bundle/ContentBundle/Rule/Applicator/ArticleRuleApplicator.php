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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;

class ArticleRuleApplicator implements RuleApplicatorInterface
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * @var array
     */
    private $supportedKeys = ['route', 'templateName'];

    /**
     * ArticleRuleApplicator constructor.
     *
     * @param RouteProviderInterface $routeProvider
     */
    public function __construct(RouteProviderInterface $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $configuration = $rule->getConfiguration();

        $this->validateRuleConfiguration($configuration, $rule);

        if (!$subject instanceof ArticleInterface) {
            throw new \Exception('Unsupported type!');
        }

        if (isset($configuration['route'])) {
            $routeId = $configuration['route'];
            $route = $this->routeProvider->getOneById($routeId);

            $this->ensureRouteExists($route);

            $subject->setRoute($route);
        }

        if (isset($configuration['templateName'])) {
            $subject->setTemplateName($configuration['templateName']);
        }
    }

    public function isSupported(RuleSubjectInterface $subject)
    {
        return $subject instanceof ArticleInterface && 'article' === $subject->getSubjectType();
    }

    /**
     * {@inheritdoc}
     */
    private function validateRuleConfiguration(array $configuration, RuleInterface $rule)
    {
        foreach ($configuration as $key => $value) {
            if (!in_array($key, $this->supportedKeys)) {
                throw new \InvalidArgumentException(sprintf(
                    'Configuration with key "%s" is not allowed by "%s" rule! Supported keys are: %s',
                    $key,
                    $rule->getValue(),
                    implode(', ', $this->supportedKeys)
                ));
            }
        }
    }

    private function ensureRouteExists(RouteInterface $route)
    {
        if (null === $route) {
            throw new \InvalidArgumentException('Route not found! Make sure the rule defines an existing route!');
        }
    }
}
