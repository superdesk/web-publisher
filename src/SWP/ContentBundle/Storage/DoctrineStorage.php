<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Storage;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use SWP\ContentBundle\Document\DocumentNotFoundException;
use SWP\ContentBundle\Document\DocumentLockedException;
use SWP\ContentBundle\Storage\StorageException;

class DoctrineStorage extends AbstractStorage
{
    /**
     * Doctrine document manager
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    public function __construct(ObjectManager $documentManager)
    {
        $this->manager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDocument($documentId, $version = null, $locale = null)
    {
        // TODO: check if we need to change null
        $document = $this->manager->find(null, $documentId);

        if (is_null($document) {
            throw new DocumentNotFoundException('Document doesn\'t exist.');
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDocuments($documentIds, $version = null, $locale = null)
    {
        // TODO: check if we need to change null
        $documents = $this->manager->find(null, $documentIds);

        if (is_null($documents) {
            throw new DocumentNotFoundException('Document doesn\'t exist.');
        }

        return $documents;
    }

    /**
     * {@inheritdoc}
     */
    public function searchDocuments($parameters, $order = null, $limit = null, $offset= null, $onlyFirst = false)
    {
        // TODO: we should supply parameter for GetRepository
        $repository = $this->manager->getRepository();

        if ($onlyFirst) {
            $documents = $repository->findOneBy($parameters);
        } else {
            $documents = $repository->findBy($parameters, $order, $limit, $offset);
        }

        if (is_null($documents) {
            throw new DocumentNotFoundException('Document doesn\'t exist.');
        }

        return $documents;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDocument($document, $forceWhenLocked = false)
    {
        if ($this->documentExists($document->getId()) && $this->documentIdLocked($document->getId()) && !$forceWhenLocked) {
            throw new DocumentLockedException('Cannot overwrite a locked Document.');
        } else {
            try {
                $this->manager->persist($document);
                $this->manager->flush();
            } catch(\Exception $e) {
                throw new StorageException('Could not save document.', $e->getCode(), $e);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDocument($documentId, $forceWhenLocked = false)
    {
        if ($this->documentIsLocked($documentId) && !$forceWhenLocked) {
            throw new DocumentLockedException('Cannot delete a locked Document.');
        }

        $document = $this->fetch($documentId);
        $this->manager->remove($document);
        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function documentExists($documentId)
    {
        return (!is_null($this->manager->find(null, $documentId)));
    }
}
