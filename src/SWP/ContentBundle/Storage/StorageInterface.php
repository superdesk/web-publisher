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

use SWP\ContentBundle\Document\VersionInterface;
use SWP\ContentBundle\Document\LocaleInterface;

interface StorageInterface
{
    /**
     * Fetch a document from the storage facility.
     *
     * @param string $class Class of the document
     * @param mixed $id Document identifier
     * @param VersionInterface|null $version Version of the document to retrieve
     * @param LocaleInterface|null Locale to retrieve
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface
     *
     * @throws \SWP\ContentBundle\Document\DocumentNotFoundException
     */
    public function fetchDocument(
        $class,
        $id,
        VersionInterface $version = null,
        LocaleInterface $locale = null
    );

    /**
     * Fetch documents from the storage facility.
     *
     * @param string|null Class of the document, when omitted will search in
     *     types of documents
     * @param mixed[] $documentIds Array of Document Identifiers
     * @param VersionInterface|null $version Filter documents by version
     * @param LocaleInterface|null Filter documents by locale
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface[]
     *
     * @throws \SWP\ContentBundle\Document\DocumentNotFoundException
     */
    public function fetchDocuments(
        $class,
        array $id,
        VersionInterface $version = null,
        LocaleInterface $locale = null
    );

    /**
     * Search documents based on a list of parameters.
     *
     * @param array|string $classes Classes in which to search (all parameters will
     *     be used on all classes)
     * @param array|null $parameters Array container parameters, the following
     *     array structure should be supported (if it makes sense for the current
     *     storage implementation), all values can also be null or keys not set:
     *         array(
     *             'orderby' => array(<fieldname>)|<fieldname>|null,
     *             'order' => array('desc|asc')|'desc|asc'|null,
     *             'limit' => [0-1]+|null,
     *             'offset' => [0-1]+|null,
     *         )
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface[]|null List of Documents or null
     */
    public function searchDocuments($classes, array $parameters = null);

    /**
     * Stores a document in the storage facility.
     *
     * @param  string $class Class where to store the document
     * @param  \SWP\ContentBundle\Document\DocumentInterface $document Document to store
     *
     * @return boolean Returns true on succes, throws exception on failure
     *
     * @throws \SWP\ContentBundle\Document\DocumentLockedException Thrown when document is locked
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageSaveException Thrown on any other error
     */
    public function saveDocument($class, DocumentInterface $document);

    /**
     * Remove a document from the storage facility.
     *
     * @param string $class Class of the document
     * @param mixed $documentId Document identifier
     * @param boolean $forceWhenLocked Force deletion eventhough Document is locked
     *
     * @return boolean Returns true on succes, throws exception on failure
     *
     * @throws \SWP\ContentBundle\Document\DocumentLockedException Thrown when document is locked
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageDeleteException
     */
    public function deleteDocument($class, $documentId, $forceWhenLocked = false);

    /**
     * Locks a document.
     *
     * @param string $class Class of the document
     * @param mixed $documentId Document identifier
     *
     * @return boolean
     *
     * @throws \SWP\ContentBundle\Document\DocumentException Thrown when
     *     document could not be unlocked
     */
    public function lockDocument($class, $documentId);

    /**
     * Unlocks a document.
     *
     * @param string $class Class of the document
     * @param mixed $documentId Document identifier
     *
     * @return boolean
     *
     * @throws \SWP\ContentBundle\Document\DocumentLockedException Thrown when
     *     the document already is locked
     * @throws \SWP\ContentBundle\Document\DocumentException Thrown when
     *     the document could not be locked
     */
    public function unlockDocument($class, $documentId);

    /**
     * Checks whether a document exists.
     *
     * @param string $class Class of the document
     * @param mixed $documentId Document identifier
     *
     * @return boolean
     */
    public function documentExists($class, $documentId);

    /**
     * Checks whether a document is locked for manipulation.
     *
     * @param string $class Class of the document
     * @param mixed $documentId Document identifier
     *
     * @return boolean
     */
    public function documentIsLocked($class, $documentId);

    /**
     * Returns whether the storage mechanism supports versioning.
     *
     * @return boolean
     */
    public function supportsVersioning();

    /**
     * Whether the storage mechanism supports locale handling.
     *
     * @return boolean
     */
    public function supportsLocale();

    /**
     * Whether locking is supported by the storage implementation. This could
     * be handling via storage library mechanism, but also handles via document
     * attribute.
     *
     * @return boolean
     */
    public function supportsLocking();
}
