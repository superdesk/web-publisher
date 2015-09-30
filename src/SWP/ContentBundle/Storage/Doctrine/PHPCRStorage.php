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
use InvalidArgumentException;
use SWP\ContentBundle\Storage\DoctrineStorage;
use SWP\ContentBundle\Document\DocumentNotFoundException;
use SWP\ContentBundle\Document\LocaleInterface;
use SWP\ContentBundle\Document\VersionInterface;
use SWP\ContentBundle\Search\SearchCriteria;

/**
 * Doctine PHPCR ODM specific implementation.
 */
class PHPCRStorage extends DoctrineStorage
{
    /**
     * Doctrine PHPCR object manager.
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

    /**
     * {@inheritdoc}
     */
    protected $supportsLocking = true;

    public function __construct(DocumentManagerInterface $documentManager)
    {
        $this->manager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDocument($class, $documentId, $version = null, $locale = null)
    {
        $queryBuilder = $this->manager->createQueryBuilder();

        if ($locale instanceof LocaleInterface) {
            $queryBuilder->setlocale($locale->getLocale());
        }

        $queryBuilder->from()->document($class);
        $queryBuilder->where()->eq()->field('id')->literal($documentId);

        if ($version instanceof VersionInterface) {
            // TODO: Check if this actually works!
            $queryBuilder->andWhere()->eq()->field('versionName')->literal($version->getVersion());
        }

        $query = $queryBuilder->getQuery();
        $document = $query->getSingleResult();

        if (is_null($document) {
            throw new DocumentNotFoundException('Document doesn\'t exist.');
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDocuments($class, $documentIds, $version = null, $locale = null)
    {
        // TODO: check if we need to change null
        $documents = $this->manager->findMany($class, $documentIds);
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
    public function searchDocuments($classes, SearchCriteria $criteria = null)
    {
        // TODO: Check if we need to overwrite this from DoctrineStorage.php or if we can just same method code
        $classes = (is_string($classes)) ? array($classes) : $classes;
        $repositories = array();
        $documents = array();

        if (!is_array($classes)) {
            throw new InvalidArgumentException('Invalid datatype for first argument.');
        }

        foreach ($classes as $class) {
            // TODO: Check if this throws an exception when repository class does not exist or smth
            $repositories[$class] = $this->manager->getRepository($class);
        }
        if (count($repositories) <= 0) {
            throw new StorageException('No repositories to select from.');
        }

        if (is_null($criteria)) {
            foreach ($repositories as $class => $repository) {
                $documents[$class] = $repository->findAll();
            }
        } else {
            foreach ($repositories as $class => $repository) {
                $documents[$class] = $repository->findBy(
                    $criteria->all(),
                    $criteria->getOrderby(),
                    $criteria->getLimit(),
                    $criteria->getOffset()
                );
            }
        }

        if (count($documents)) {
            throw new DocumentNotFoundException('No documents found.');
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
                $this->manager->checkpoint($document);
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
    public function deleteDocument($class, $documentId, $forceWhenLocked = false)
    {
        if ($this->documentIsLocked($documentId) && !$forceWhenLocked) {
            throw new DocumentLockedException('Cannot delete a locked Document.');
        }

        $document = $this->fetch($class, $documentId);
        $this->manager->remove($document);
        $this->manager->flush($document);
    }

    /**
     * {@inheritdoc}
     */
    public function lockDocument($class, $documentId)
    {
        return $this->manager->checkin($class, $documentId);
    }

    /**
     * {@inheritdoc}
     */
    public function unlockDocument($class, $documentId)
    {
        return $this->manager->checkout($class, $documentId);
    }

    /**
     * {@inheritdoc}
     */
    public function documentExists($class, $documentId)
    {
        return (!is_null($this->manager->find($class, $documentId)));
    }
}
