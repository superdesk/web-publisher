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

namespace SWP\Component\Rule\Evaluator;

use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;

/**
 *  Rule evaluator evaluates rule on given object and makes sure it matches the criteria.
 *
 * Interface RuleEvaluatorInterface.
 */
interface RuleEvaluatorInterface
{
    /**
     * @param RuleInterface        $rule
     * @param RuleSubjectInterface $subject
     *
     * @return mixed
     */
    public function evaluate(RuleInterface $rule, RuleSubjectInterface $subject);
}
