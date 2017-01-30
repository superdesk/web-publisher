<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\ORM\Query;
use SWP\Component\Revision\Context\RevisionContext;
use SWP\Bundle\TemplatesSystemBundle\Provider\WidgetProvider as BaseWidgetProvider;
use SWP\Component\Common\Criteria\Criteria;

/**
 * Revisions Aware WidgetProvider.
 */
class WidgetProvider extends BaseWidgetProvider
{
    /**
     * @var RevisionContext
     */
    protected $revisionContext;

    /**
     * {@inheritdoc}
     */
    public function getQueryForAll(): Query
    {
        $criteria = new Criteria();
        $criteria->set('revision', $this->revisionContext->getRevision());

        return $this->widgetRepository->getQueryByCriteria($criteria, [], 'w');
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById(int $id)
    {
        return $this->widgetRepository->getById($id)
            ->andWhere('w.revision = :revision')
            ->setParameter('revision', $this->revisionContext->getRevision())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param RevisionContext $revisionContext
     */
    public function setRevisionContext(RevisionContext $revisionContext)
    {
        $this->revisionContext = $revisionContext;
    }
}
