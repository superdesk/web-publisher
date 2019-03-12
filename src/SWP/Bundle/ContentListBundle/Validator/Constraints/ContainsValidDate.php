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

final class ContainsValidDate extends Constraint
{
    public $message = 'The value "%value%" contains not valid date string ("%date%"): it can only contain date in format YYYY-MM-DD';
}
