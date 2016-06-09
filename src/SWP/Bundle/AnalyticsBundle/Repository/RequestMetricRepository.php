<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\AnalyticsBundle\Repository;

use SWP\Bundle\AnalyticsBundle\Model\RequestMetric;

class RequestMetricRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param RequestMetric $requestMetric
     * Persists object and flushes the entity manager
     */
    public function save(RequestMetric $requestMetric)
    {
        $this->_em->persist($requestMetric);
        $this->_em->flush();
    }
}
