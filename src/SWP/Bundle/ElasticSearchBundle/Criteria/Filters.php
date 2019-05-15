<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher ElasticSearch Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ElasticSearchBundle\Criteria;

use Symfony\Component\HttpFoundation\ParameterBag;

final class Filters extends AbstractCriteria
{
    /**
     * @var ParameterBag
     */
    private $fields;

    /**
     * @param array $fields
     */
    private function __construct(array $fields)
    {
        $this->fields = new ParameterBag($fields);
    }

    /**
     * @param array $queryParameters
     *
     * @return Filters
     */
    public static function fromQueryParameters(array $queryParameters)
    {
        $fields = $queryParameters;
        unset($fields['page'], $fields['per_page'], $fields['sort']);

        $convertedFields = [];
        foreach ($fields as $field) {
            $convertedFields[] = is_string($field) ? self::camelize($field) : $field;
        }

        return new self($convertedFields);
    }

    /**
     * @return ParameterBag
     */
    public function getFields()
    {
        return $this->fields;
    }
}
