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

namespace SWP\ContentBundle\Storage\Doctrine;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use SWP\ContentBundle\Storage\DoctrineStorage;
use SWP\ContentBundle\Document\DocumentNotFoundException;

class PHPCRStorage extends DoctrineStorage
{
    /**
     * Doctrine document manager
     *
     * @var \Doctrine\ODM\PHPCR\DocumentManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    protected $supportsVersioning = true;

    /**
     * {@inheritdoc}
     */
    protected $supportsLocale = true;

    public function __construct(DocumentManagerInterface $documentManager)
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
        // TODO: Built in fetching versions
        // TODO: Built in fetching specific locale

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
        $documents = $this->manager->findMany(null, $documentIds);
        // TODO: Built in fetching versions (not sure if possible with multiple documents)
        // TODO: Built in fetching specific locale

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
                // TODO: Automatically create new version on each save (maybe via parameter?)
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
