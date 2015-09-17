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
     * Fetch a document from the storage facility
     *
     * @param mixed $documentId Document identifier
     * @param VersionInterface|null $version Version of the document to retrieve
     * @param LocaleInterface Locale to retrieve
     *
     * @return \SWP\ContentBundle\Document\DocumentInterface
     * @throws \SWP\ContentBundle\Document\DocumentNotFoundException
     */
    public function fetchDocument($documentId, $version = null, $locale = null);

    /**
     * Fetch multiple documents based on a list of parameters
     *
     * @param  array $parameters Array container parameters
     *
     * @return [\SWP\ContentBundle\Document\DocumentInterface]|null List of Documents or null
     */
    public function fetchDocuments($parameters);

    /**
     * Stores a document in the storage facility
     *
     * @param  \SWP\ContentBundle\Document\DocumentInterface $document
     *
     * @return boolean Returns true on succes, throws exception on failure
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageSaveException
     */
    public function saveDocument($document);

    /**
     * Remove a document from the storage facility
     *
     * @param  mixed $documentId Document identifier
     *
     * @return boolean Returns true on succes, throws exception on failure
     * @throws \SWP\ContentBundle\Storage\StorageException\StorageDeleteException
     */
    public function deleteDocument($documentId);

    /**
     * Checks whether a document exists
     *
     * @param  mixed $documentId Document identifier
     *
     * @return boolean
     */
    public function documentExists($documentId);
}
