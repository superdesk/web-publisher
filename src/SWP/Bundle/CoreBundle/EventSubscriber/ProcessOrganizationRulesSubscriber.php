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

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\CoreBundle\Matcher\RulesMatcherInterface;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Bundle\CoreBundle\Model\PublishDestination;
use SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Events;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProcessOrganizationRulesSubscriber implements EventSubscriberInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $ruleProcessor;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var PublishDestinationProviderInterface
     */
    private $publishDestinationProvider;

    /**
     * @var ArticlePublisherInterface
     */
    private $articlePublisher;

    /**
     * @var RulesMatcherInterface
     */
    private $rulesMatcher;

    public function __construct(
        RuleProcessorInterface $ruleProcessor,
        EventDispatcherInterface $eventDispatcher,
        PublishDestinationProviderInterface $publishDestinationProvider,
        ArticlePublisherInterface $articlePublisher,
        RulesMatcherInterface $rulesMatcher
    ) {
        $this->ruleProcessor = $ruleProcessor;
        $this->eventDispatcher = $eventDispatcher;
        $this->publishDestinationProvider = $publishDestinationProvider;
        $this->articlePublisher = $articlePublisher;
        $this->rulesMatcher = $rulesMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PACKAGE_POST_CREATE => 'processRules',
            Events::PACKAGE_POST_UPDATE => 'processRules',
        ];
    }

    public function processRules(GenericEvent $event): void
    {
        $package = $event->getSubject();
        $destinationsCount = $this->publishDestinationProvider->countDestinationsByPackageGuid($package);

        if (0 < $destinationsCount) {
            $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
            $result = $this->rulesMatcher->getMatchedRules($package);
            $publishAction = new CompositePublishAction($this->createDestinations($result));

            $this->articlePublisher->publish($package, $publishAction);
            $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);

            return;
        }

        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $this->ruleProcessor->process($package);
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
    }

    private function createDestinations(array $result): array
    {
        $destinations = [];
        foreach ((array) $result['tenants'] as $tenant) {
            $destination = new PublishDestination();
            $destination->setTenant($tenant['tenant']);
            if (isset($tenant['route'])) {
                $destination->setRoute($tenant['route']);
            }

            $destination->setPublished($tenant['published'] ?? false);
            $destination->setPaywallSecured($tenant['paywall_secured'] ?? false);
            $destination->setIsPublishedFbia($tenant['is_published_fbia'] ?? false);
            $destination->setContentLists($tenant['content_lists'] ?? []);
            $destination->setIsPublishedToAppleNews($tenant['is_published_to_apple_news'] ?? false);

            $destinations[] = $destination;
        }

        return $destinations;
    }
}
