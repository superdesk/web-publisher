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
     * ProcessArticleRulesSubscriber constructor.
     *
     * @param RuleProcessorInterface   $ruleProcessor
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RuleProcessorInterface $ruleProcessor,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->ruleProcessor = $ruleProcessor;
        $this->eventDispatcher = $eventDispatcher;
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

    /**
     * @param GenericEvent $event
     */
    public function processRules(GenericEvent $event)
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $this->ruleProcessor->process($event->getSubject());
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
    }
}
