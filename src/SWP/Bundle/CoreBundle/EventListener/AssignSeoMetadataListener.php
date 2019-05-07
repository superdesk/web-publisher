<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class AssignSeoMetadataListener
{
    private $seoMetadataRepository;

    public function __construct(RepositoryInterface $seoMetadataRepository)
    {
        $this->seoMetadataRepository = $seoMetadataRepository;
    }

    public function assignSeoMetadata(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        $package = $event->getPackage();

        $seoMetadata = $this->seoMetadataRepository->findOneBy(['packageGuid' => $package->getGuid()]);

        if (null === $seoMetadata) {
            return;
        }

        $article->setSeoMetadata(null);

        $newSeoMetadata = clone $seoMetadata;
        $newSeoMetadata->setPackageGuid(null);
        $newSeoMetadata->setId(null);

        $article->setSeoMetadata($seoMetadata);
    }
}
