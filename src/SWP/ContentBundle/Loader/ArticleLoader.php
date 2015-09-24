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

namespace SWP\ContentBundle\Loader;

use Doctrine\ODM\PHPCR\DocumentManager;
use SWP\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\TemplatesSystem\Gimme\Meta\Meta;

class ArticleLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param string $rootDir path to application root directory
     */
    public function __construct($rootDir, DocumentManager $dm)
    {
        $this->rootDir = $rootDir;
        $this->dm = $dm;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Loader loads articles from Content Repository",
     *     parameters={
     *         contentPath="SINGLE|required content path"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|Meta[]|bool false if meta cannot be loaded, a Meta instance otherwise
     */
    public function load($type, $parameters, $responseType = LoaderInterface::SINGLE)
    {
        if ($responseType === LoaderInterface::SINGLE) {
            $article = $this->dm->find('SWP\ContentBundle\Document\Article', $parameters['contentPath']);
            if ($article) {
                return new Meta($this->rootDir.'/Resources/meta/article.yml', $article);
            }
        }

        return false;
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported($type)
    {
        return in_array($type, ['articles', 'article']);
    }
}
