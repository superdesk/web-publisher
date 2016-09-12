<?php

namespace SWP\Component\Rule\Processor;

use SWP\Component\Rule\Model\RuleSubjectInterface;

interface RuleProcessorInterface
{
    public function process(RuleSubjectInterface $subject);
}
