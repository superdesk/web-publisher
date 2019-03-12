<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ContainsValidDateValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    private $dateFilters = ['publishedAt', 'publishedBefore', 'publishedAfter'];

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        foreach ($this->dateFilters as $filter) {
            if (isset($value[$filter]) && !$this->isValidDate($value[$filter])) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%value%', $filter)
                    ->setParameter('%date%', $value[$filter])
                    ->addViolation();
            }
        }
    }

    private function isValidDate(string $date)
    {
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $date);

            if ($dateTime && $dateTime->format('Y-m-d') === $date) {
                return true;
            }
        }

        return false;
    }
}
