<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Service;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RuleEvaluator implements RuleEvaluatorInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $language;

    public function __construct()
    {
        $this->language = new ExpressionLanguage();
    }

    /**
     * @param $rule
     * @param array $params
     *
     * @return string
     */
    public function evaluate($rule, array $params = [])
    {
        return $this->language->evaluate($rule, $params);
    }
}
