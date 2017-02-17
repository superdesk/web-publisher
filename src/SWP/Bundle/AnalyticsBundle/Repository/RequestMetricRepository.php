<?php

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Repository;

use SWP\Bundle\AnalyticsBundle\Model\RequestMetric;

class RequestMetricRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param RequestMetric $requestMetric Persists object and flushes the entity manager
     */
    public function persistAndFlush(RequestMetric $requestMetric)
    {
        $this->_em->persist($requestMetric);
        $this->_em->flush($requestMetric);
    }
}
