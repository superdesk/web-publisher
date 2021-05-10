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

namespace SWP\Bundle\CoreBundle\Rule\Processor;

use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class TenantAwareRuleProcessor implements RuleProcessorInterface
{
    /**
     * @var RuleProcessorInterface
     */
    private $decoratedRuleProcessor;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * TenantAwareRuleProcessor constructor.
     *
     * @param RuleProcessorInterface   $decoratedRuleProcessor
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(RuleProcessorInterface $decoratedRuleProcessor, EventDispatcherInterface $eventDispatcher)
    {
        $this->decoratedRuleProcessor = $decoratedRuleProcessor;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function process(RuleSubjectInterface $subject)
    {
        $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);
        $this->decoratedRuleProcessor->process($subject);
        $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);
    }
}
