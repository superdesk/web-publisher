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

namespace SWP\ContentBundle\Repository;

use SWP\ContentBundle\Model\Page;

/**
 * PageRepository.
 */
class PageContentRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get Query for Page searched by page.
     *
     * @param Page $page
     *
     * @return \Doctrine\ORM\Query
     */
    public function getForPage($page)
    {
        $qb = $this->createQueryBuilder('pa')
            ->where('pa.page = :page')
            ->setParameters([
                'page' => $page,
            ]);

        return $qb->getQuery();
    }

    /**
     * Get Query for Page searched by contentPath.
     *
     * @param string $contentPath
     *
     * @return \Doctrine\ORM\Query
     */
    public function getByContentPath($contentPath)
    {
        $qb = $this->createQueryBuilder('pa')
            ->where('pa.contentPath = :contentPath')
            ->setParameters([
                'contentPath' => $contentPath,
            ]);

        return $qb->getQuery();
    }
}
