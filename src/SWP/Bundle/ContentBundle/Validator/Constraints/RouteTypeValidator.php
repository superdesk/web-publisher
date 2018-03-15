<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Validator\Constraints;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RouteTypeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $supportedTypes = [RouteInterface::TYPE_COLLECTION, RouteInterface::TYPE_CONTENT, RouteInterface::TYPE_CUSTOM];

        if (!in_array($value, $supportedTypes)) {
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '%type%' => $value,
                    '%supportedTypes%' => implode(', ', $supportedTypes),
                ])
                ->addViolation()
            ;
        }
    }
}
