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

namespace SWP\Component\Rule\Processor;

use SWP\Component\Rule\Model\RuleSubjectInterface;

/**
 * Rule Processor ensures that each rule will be processed
 * and if the corresponding object matches given rule's configuration/criteria
 * it is then applied to this object.
 *
 * Interface RuleProcessorInterface.
 */
interface RuleProcessorInterface
{
    /**
     * @param RuleSubjectInterface $subject
     */
    public function process(RuleSubjectInterface $subject);
}
