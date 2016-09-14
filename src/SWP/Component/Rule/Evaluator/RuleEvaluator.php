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

namespace SWP\Component\Rule\Evaluator;

use Psr\Log\LoggerInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class RuleEvaluator implements RuleEvaluatorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ExpressionLanguage
     */
    private $expression;

    /**
     * RuleEvaluator constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->expression = new ExpressionLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        try {
            return $this->expression->evaluate($rule->getValue(), [$subject->getSubjectType() => $subject]);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return false;
    }
}
