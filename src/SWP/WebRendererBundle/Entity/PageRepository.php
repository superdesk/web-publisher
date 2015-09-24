<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\WebRendererBundle\Entity;

/**
 * PageRepository.
 */
class PageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get Query for Page searched by id.
     *
     * @param int $pageId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getById($pageId)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.id = :pageId')
            ->setParameters([
                'pageId' => $pageId,
            ]);

        return $qb->getQuery();
    }
}
