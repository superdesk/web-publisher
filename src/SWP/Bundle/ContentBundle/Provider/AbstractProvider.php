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

namespace SWP\Bundle\ContentBundle\Provider;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Criteria\Criteria;

/**
 * Class AbstractProvider.
 */
abstract class AbstractProvider
{
    /**
     * @param Criteria $criteria
     *
     * @return Collection
     */
    public function getManyByCriteria(Criteria $criteria): Collection
    {
        $result = $this->getRepository()
            ->getArticlesByCriteria($criteria, $criteria->get('order', []))->getQuery()->getResult();

        return $result;
    }
}
