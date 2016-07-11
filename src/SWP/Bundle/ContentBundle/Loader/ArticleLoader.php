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
namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Yaml\Parser;

class ArticleLoader implements LoaderInterface
{
    protected $serviceContainer;

    public function __construct($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
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
        $dm = $this->serviceContainer->get('doctrine_phpcr.odm.document_manager');
        $configurationPath = $this->serviceContainer->getParameter('kernel.root_dir').'/Resources/meta/article.yml';
        $metadataCache = $this->serviceContainer->get('doctrine_cache.providers.main_cache');

        $article = null;
        if (empty($parameters)) {
            $parameters = [];
        }

        // Cache meta configuration
        $cacheKey = md5($configurationPath);
        if (!$metadataCache->contains($cacheKey)) {
            if (!is_readable($configurationPath)) {
                throw new \InvalidArgumentException('Configuration file is not readable for parser');
            }
            $yaml = new Parser();
            $configuration = $yaml->parse(file_get_contents($configurationPath));
            $metadataCache->save($cacheKey, $configuration);
        } else {
            $configuration = $metadataCache->fetch($cacheKey);
        }

        if ($responseType === LoaderInterface::SINGLE) {
            if (array_key_exists('contentPath', $parameters)) {
                $article = $dm->find('SWP\Bundle\ContentBundle\Document\Article', $parameters['contentPath']);
            } elseif (array_key_exists('article', $parameters)) {
                $article = $parameters['article'];
            } elseif (array_key_exists('slug', $parameters)) {
                $article = $dm->getRepository('SWP\Bundle\ContentBundle\Document\Article')
                    ->findOneBy(['slug' => $parameters['slug']]);
            }

            if (!is_null($article)) {
                return new Meta($configuration, $article);
            }
        } elseif ($responseType === LoaderInterface::COLLECTION) {
            if (array_key_exists('route', $parameters)) {
                $pathBuilder = $this->serviceContainer->get('swp_multi_tenancy.path_builder');
                $route = $dm->find(null, $pathBuilder->build(
                    $this->serviceContainer->getParameter(
                        'swp_multi_tenancy.persistence.phpcr.route_basepaths'
                    )[0].$parameters['route']
                ));

                if ($route) {
                    $articles = $dm->getReferrers($route, null, null, null, 'SWP\Bundle\ContentBundle\Document\Article');
                    $metas = [];
                    foreach ($articles as $article) {
                        if (!is_null($article)) {
                            $metas[] = new Meta(
                                $configuration,
                                $article
                            );
                        }
                    }

                    return $metas;
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
        return in_array($type, ['articles', 'article']);
    }
}
