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

namespace SWP\ContentBundle\Manager;

use SWP\ContentBundle\Document\Article;

class ArticleManager extends AbstractManager
{
    /**
     * Class name of the default document type
     *
     * @var string
     */
    protected $documentClassName = '\SWP\ContentBundle\Document\Article';

    /**
     * Saves and article in storage
     *
     * @param  Article $article
     */
    public function save(Article $article)
    {
        $this->storage->save($this->documentClassName, $article);
    }

    /**
     * Lock an article from performing any action on it.
     *
     * @param  mixed $id Article identifier
     *
     * @return boolean
     */
    public function lock($id)
    {
        return $this->storage->lock($this->documentClassName, $id);
    }

    /**
     * Unlock an article.
     *
     * @param  mixed $id Article identifier
     *
     * @return boolean
     */
    public function unlock($id)
    {
        return $this->storage->unlock($this->documentClassName, $id);
    }

    /**
     * Removes an article.
     *
     * @param  mixed $id Article identifier
     *
     * @return boolean
     */
    public function remove($id)
    {
        // TODO: Add lock override
        // TODO: Check if we need to built-in locked mechanism check
        return $this->storage->remove($this->documentClassName, $id);
    }

    /**
     * Find an Article by id, with the possibility of on filtering a specific
     * version or locale.
     *
     * @param  mixed $id Article identifier
     * @param  mixed $version Version identifier
     * @param  mixed $locale Locale identifier
     *
     * @return Article Returns article or throws exception
     */
    public function findOne($id, $version = null, $locale = null)
    {
        if (!is_null($version)) {
            $version = new $this->version($version);
        }

        if (!is_null($locale)) {
            $locale = new $this->locale($locale);
        }

        return $this->storage->fetchDocument($this->$documentClassName, $id, $version, $locale);
    }

    /**
     * Find multiples articles by their ids
     *
     * @param  mixed $ids Article identifiers
     * @param  mixed $version Version identifier
     * @param  mixed $locale Locale identifier
     *
     * @return Article[] Returns array containing articles or throws exception
     */
    public function findMany($ids, $version = null, $locale = null)
    {
        if (!is_null($version)) {
            $version = new $this->version($version);
        }

        if (!is_null($locale)) {
            $locale = new $this->locale($locale);
        }

        return $this->storage->fetchDocuments($this->documentClassName, $ids, $version, $locale;
    }

    public function search()
    {
        //
    }

    public function searchByDate()
    {
        //
    }
}
