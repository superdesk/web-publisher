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

use SWP\Bundle\CoreBundle\Factory\PublishActionFactoryInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Rule\PublishDestinationResolverInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Rule\Applicator\AbstractRuleApplicator;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PackageRuleApplicator extends AbstractRuleApplicator
{
    /**
     * @var ArticlePublisherInterface
     */
    private $articlePublisher;

    /**
     * @var PublishDestinationResolverInterface
     */
    private $publishDestinationResolver;

    /**
     * @var PublishActionFactoryInterface
     */
    private $publishActionFactory;

    /**
     * @var array
     */
    private $supportedKeys = ['destinations'];

    /**
     * PackageRuleApplicator constructor.
     *
     * @param ArticlePublisherInterface           $articlePublisher
     * @param PublishDestinationResolverInterface $publishDestinationResolver
     * @param PublishActionFactoryInterface       $publishActionFactory
     */
    public function __construct(
        ArticlePublisherInterface $articlePublisher,
        PublishDestinationResolverInterface $publishDestinationResolver,
        PublishActionFactoryInterface $publishActionFactory
    ) {
        $this->articlePublisher = $articlePublisher;
        $this->publishDestinationResolver = $publishDestinationResolver;
        $this->publishActionFactory = $publishActionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $configuration = $this->validateRuleConfiguration($rule->getConfiguration());

        if ($subject instanceof PackageInterface && !empty($configuration)) {
            if (ContentInterface::STATUS_CANCELED === $subject->getPubStatus()) {
                return;
            }

            foreach ($configuration[$this->supportedKeys[0]] as $value) {
                if (empty($this->validateDestinationConfig($value))) {
                    return;
                }
            }

            $destinations = [];
            foreach ($configuration[$this->supportedKeys[0]] as $destination) {
                $destinations[] = $this->publishDestinationResolver->resolve(
                    $destination['tenant'],
                    $destination['route']
                );
            }

            $publishAction = $this->publishActionFactory->createWithDestinations($destinations);

            $this->articlePublisher->publish($subject, $publishAction);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(RuleSubjectInterface $subject)
    {
        if (!$subject instanceof PackageInterface && 'package' === $subject->getSubjectType()) {
            $this->logger->warning(sprintf(
                '"%s" is not supported by "%s" rule applicator!',
                is_object($subject) ? get_class($subject) : gettype($subject),
                get_class($this)
            ));

            return false;
        }

        return true;
    }

    private function validateDestinationConfig(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('tenant');
        $resolver->setDefined('route');

        return $this->resolveConfig($resolver, $config);
    }

    private function validateRuleConfiguration(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->supportedKeys[0]);

        return $this->resolveConfig($resolver, $config);
    }
}
