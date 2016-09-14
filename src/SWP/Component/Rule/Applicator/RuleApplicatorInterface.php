<?php

/**
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

use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Model\RuleInterface;

/**
 * Rule applicator is used to apply rule configuration if it matches.
 * E.g. assign route to newly created articles.
 *
 * Interface RuleApplicatorInterface.
 */
interface RuleApplicatorInterface
{
    /**
     * @param RuleInterface        $rule
     * @param RuleSubjectInterface $subject
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject);

    /**
     * @param RuleSubjectInterface $subject
     *
     * @return bool
     */
    public function isSupported(RuleSubjectInterface $subject);
}
