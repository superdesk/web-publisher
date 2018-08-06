<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Doctrine;

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface SlideshowRepositoryInterface extends RepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): array;

    public function countByCriteria(Criteria $criteria): int;
}
