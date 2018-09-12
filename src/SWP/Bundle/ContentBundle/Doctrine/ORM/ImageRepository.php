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

use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository implements ImageRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findImageByAssetId(string $assetId)
    {
        $images = $this->createQueryBuilder('i')
            ->where('i.assetId = :assetId')
            ->setParameter('assetId', $assetId)
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();

        if (0 === count($images)) {
            return;
        }

        return $images[0];
    }
}
