<?php

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface RouteRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $candidates
     * @param array $orderBy
     *
     * @return QueryBuilder
     */
    public function getChildrenByStaticPrefix(array $candidates, array $orderBy): QueryBuilder;

    public function countByCriteria(Criteria $criteria): int;
}
