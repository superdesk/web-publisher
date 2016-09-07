<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(PersistableInterface $object)
    {
        $this->dm->persist($object);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PersistableInterface $object)
    {
        if (null !== $this->find($object->getId())) {
            $this->dm->remove($object);
            $this->dm->flush($object);
        }
    }
}
