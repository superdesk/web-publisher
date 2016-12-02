<?php

declare(strict_types=1);

/*
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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository implements ImageRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        return $this->getQueryByCriteria($criteria, $sorting, 'i');
    }
}
