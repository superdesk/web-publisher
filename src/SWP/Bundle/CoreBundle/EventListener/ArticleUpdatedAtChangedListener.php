<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\Package;

final class ArticleUpdatedAtChangedListener
{
    private $elasticaObjectPersister;

    public function __construct(ObjectPersisterInterface $elasticaObjectPersister)
    {
        $this->elasticaObjectPersister = $elasticaObjectPersister;
    }

    public function postUpdate(ArticleInterface $article, LifecycleEventArgs $event): void
    {
        if (null === ($updatedAt = $article->getUpdatedAt())) {
            return;
        }

        if (null === ($package = $article->getPackage())) {
            return;
        }

        $entityManager = $event->getEntityManager();

        $query = $entityManager->createQuery('UPDATE '.Package::class.' s SET s.updatedAt = :updatedAt WHERE s.id = :id');
        $query->setParameter('id', $package->getId());
        $query->setParameter('updatedAt', $updatedAt);
        $query->execute();

        $this->elasticaObjectPersister->replaceOne($package);
    }
}
