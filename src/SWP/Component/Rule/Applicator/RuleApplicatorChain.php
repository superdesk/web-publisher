<?php

/*
 * This file is part of the Superdesk Web Publisher Rule Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Rule\Applicator;

use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;

final class RuleApplicatorChain implements RuleApplicatorInterface
{
    /**
     * @var RuleApplicatorInterface[] array
     */
    private $ruleApplicators = [];

    /**
     * RuleApplicatorChain constructor.
     *
     * @param array $ruleApplicators
     */
    public function __construct(array $ruleApplicators = [])
    {
        $this->ruleApplicators = $ruleApplicators;
    }

    /**
     * @param RuleApplicatorInterface $ruleApplicator
     */
    public function addApplicator(RuleApplicatorInterface $ruleApplicator)
    {
        $this->ruleApplicators[] = $ruleApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        foreach ($this->ruleApplicators as $ruleApplicator) {
            if ($ruleApplicator->isSupported($subject)) {
                $ruleApplicator->apply($rule, $subject);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(RuleSubjectInterface $subject)
    {
        foreach ($this->ruleApplicators as $ruleApplicator) {
            if ($ruleApplicator->isSupported($subject)) {
                return true;
            }
        }

        return false;
    }
}
