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

use SWP\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\TemplatesSystem\Gimme\Meta\Meta;
use Doctrine\ODM\PHPCR\DocumentManager;

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

    protected $em;

    /**
     * @param string $rootDir path to application root directory
     */
    public function __construct($rootDir, DocumentManager $dm, $em)
    {
        $this->rootDir = $rootDir;
        $this->dm = $dm;
        $this->em = $em;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Loader loads articles from Content Repository",
     *     parameters={
     *         contentPath="SINGLE|required content path",
     *         slug="SINGLE|required content slug",
     *         pageName="COLLECTiON|name of Page for required articles"
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
        $article = null;
        if (empty($parameters)) {
            $parameters = [];
        }

        if ($responseType === LoaderInterface::SINGLE) {
            if (array_key_exists('contentPath', $parameters)) {
                $article = $this->dm->find('SWP\ContentBundle\Document\Article', $parameters['contentPath']);
            } elseif (array_key_exists('slug', $parameters)) {
                $article = $this->dm->getRepository('SWP\ContentBundle\Document\Article')
                    ->findOneBy(array('slug' => $parameters['slug']));
            }

            if (!is_null($article)) {
                return new Meta($this->rootDir.'/Resources/meta/article.yml', $article);
            }
        } elseif ($responseType === LoaderInterface::COLLECTION) {
            if (array_key_exists('pageName', $parameters)) {
                $page = $this->em->getRepository('SWP\ContentBundle\Model\Page')->getByName($parameters['pageName'])
                    ->getOneOrNullResult();

                if ($page) {
                    $articlePages = $this->em->getRepository('SWP\ContentBundle\Model\PageContent')
                        ->getForPage($page)
                        ->getResult();

                    $articles = [];
                    foreach ($articlePages as $articlePage) {
                        $article = $this->dm->find('SWP\ContentBundle\Document\Article', $articlePage->getContentPath());

                        if (!is_null($article)) {
                            $articles[] = new Meta(
                                $this->rootDir.'/Resources/meta/article.yml',
                                $article
                            );
                        }
                    }

                    return $articles;
                }
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
        return in_array($type, array('articles', 'article'));
    }
}
