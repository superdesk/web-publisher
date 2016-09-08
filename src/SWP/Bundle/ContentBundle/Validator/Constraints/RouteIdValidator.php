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

use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class RouteIdValidator extends ConstraintValidator
{
    private $provider;

    public function __construct(RouteProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function validate($value, Constraint $constraint)
    {
        $path = null;
        try {
            $path = $this->provider->getOneById($value);
        } catch (\Exception $e) {
        }

        if (null == $path) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}
