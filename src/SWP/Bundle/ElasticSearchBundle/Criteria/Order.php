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

final class Order
{
    const DEFAULT_FIELD = 'id';
    const DEFAULT_DIRECTION = self::ASCENDING_DIRECTION;
    const ASCENDING_DIRECTION = 'asc';
    const DESCENDING_DIRECTION = 'desc';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * @param string $field
     * @param string $direction
     */
    private function __construct($field, $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    /**
     * @param array $parameters
     *
     * @return Order
     */
    public static function fromQueryParameters(array $parameters)
    {
        $field = isset($parameters['sort']) ? $parameters['sort'] : self::DEFAULT_FIELD;
        $direction = self::DEFAULT_DIRECTION;
        if ('-' === $field[0]) {
            $direction = self::DESCENDING_DIRECTION;
            $field = trim($field, '-');
        }

        return new self($field, $direction);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }
}
