<?php

/**
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

use Symfony\Component\Validator\Constraint;

class RouteType extends Constraint
{
    public $message = 'swp.route.type.route_type';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
