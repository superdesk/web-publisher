<?php

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface RouteRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $candidates
     * @param array $orderBy
     *
     * @return QueryBuilder
     */
    public function getChildrensByStaticPrefix(array $candidates, array $orderBy): QueryBuilder;
}
