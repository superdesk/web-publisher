<?php

namespace SWP\Component\Rule\Model;

/**
 * This interface should be implemented by every class, which should have it's own rules.
 *
 * Interface RuleSubjectInterface.
 */
interface RuleSubjectInterface
{
    /**
     * @return string
     */
    public function getSubjectType();
}
