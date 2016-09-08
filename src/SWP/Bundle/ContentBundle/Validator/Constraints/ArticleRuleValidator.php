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
namespace SWP\Bundle\ContentBundle\Validator\Constraints;

use SWP\Bundle\ContentBundle\Model\Article;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ArticleRuleValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $article = new Article();
        $language = new ExpressionLanguage();

        try {
            $language->evaluate($value, ['article' => $article]);
        } catch (\Exception $e) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
