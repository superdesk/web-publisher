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
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleMediaProviderInterface
{
    /**
     * Gets article media repository.
     *
     * @return ArticleMediaRepositoryInterface
     */
    public function getRepository(): ArticleMediaRepositoryInterface;

    /**
     * Gets the article by id.
     *
     * @param $id
     *
     * @return ArticleInterface
     */
    public function getOneById($id);

    /**
     * @param $criteria
     *
     * @return ArticleMediaInterface
     */
    public function getOneByCriteria(Criteria $criteria): ArticleMediaInterface;

    /**
     * @param $criteria
     *
     * @return Collection
     */
    public function getManyByCriteria(Criteria $criteria): Collection;

    /**
     * @param Criteria $criteria
     *
     * @return int
     */
    public function getCountByCriteria(Criteria $criteria): int;
}
