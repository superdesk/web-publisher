<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Component\Common\Criteria\Criteria;

/**
 * Abstract class PaginatedLoader.
 */
abstract class PaginatedLoader
{
    public function applyPaginationToCriteria(Criteria $criteria, array $parameters): Criteria
    {
        if (array_key_exists('limit', $parameters) && is_numeric($parameters['limit'])) {
            $criteria->set('maxResults', (int) $parameters['limit']);
        }

        if (array_key_exists('start', $parameters) && is_numeric($parameters['start'])) {
            $criteria->set('firstResult', (int) $parameters['start']);
        }

        if (array_key_exists('order', $parameters)) {
            if (count($parameters['order']) == 2) {
                $criteria->set('order', [$parameters['order'][0] => $parameters['order'][1]]);
            }
        }

        return $criteria;
    }
}
