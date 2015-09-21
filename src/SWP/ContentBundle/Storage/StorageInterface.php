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
     * @param mixed $documentId Document identifier
     * @param VersionInterface|null $version Version of the document to retrieve
     * @param LocaleInterface Locale to retrieve
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface
     * @throws \SWP\ContentBundle\Document\DocumentNotFoundException
     */
    public function fetchDocument(
        $documentId,
        VersionInterface $version = null,
        LocaleInterface $locale = null
    );

    /**
     * Fetch documents from the storage facility.
     *
     * @param mixed[] $documentIds Array of Document Identifiers
     * @param VersionInterface|null $version Filter documents by version
     * @param LocaleInterface Filter documents by locale
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface[]
     * @throws \SWP\ContentBundle\Document\DocumentNotFoundException
     */
    public function fetchDocuments(
        $documentId,
        VersionInterface $version = null,
        LocaleInterface $locale = null
    );

    /**
     * Search documents based on a list of parameters.
     *
     * @param array|null $parameters Array container parameters
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface[]|null List of Documents or null
     */
    // public function searchDocuments(array $parameters = null);
    public function searchDocuments($parameters, $order = null, $limit = null, $offset= null, $onlyFirst = false)

    /**
     * Stores a document in the storage facility.
     *
     * @param  \SWP\ContentBundle\Document\DocumentInterface $document
     *
     * @return boolean Returns true on succes, throws exception on failure
     * @throws \SWP\ContentBundle\Document\DocumentLockedException Thrown when document is locked
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageSaveException Thrown on any other error
     */
    public function saveDocument(DocumentInterface $document);

    /**
     * Remove a document from the storage facility
     *
     * @param mixed $documentId Document identifier
     * @param boolean $forceWhenLocked Force deletion eventhough Document is locked
     *
     * @return boolean Returns true on succes, throws exception on failure
     * * @throws \SWP\ContentBundle\Document\DocumentLockedException Thrown when document is locked
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageDeleteException
     */
    public function deleteDocument($documentId, $forceWhenLocked = false);

    /**
     * Checks whether a document exists.
     *
     * @param  mixed $documentId Document identifier
     *
     * @return boolean
     */
    public function documentExists($documentId);

    /**
     * Checks whether a document is locked for manipulation.
     *
     * @param  mixed $documentId Document identifier
     *
     * @return boolean
     */
    public function documentIsLocked($documentId);

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
}
