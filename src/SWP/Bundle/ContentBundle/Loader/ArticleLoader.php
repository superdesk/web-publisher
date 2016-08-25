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

use PHPCR\Query\QueryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Doctrine\ODM\PHPCR\DocumentManager;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

class ArticleLoader implements LoaderInterface
{
    /**
     * @var PublishWorkflowChecker
     */
    protected $publishWorkflowChecker;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var string
     */
    protected $routeBasepaths;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * ArticleLoader constructor.
     *
     * @param PublishWorkflowChecker          $publishWorkflowChecker
     * @param DocumentManager                 $dm
     * @param TenantAwarePathBuilderInterface $pathBuilder
     * @param string                          $routeBasepaths
     * @param MetaFactoryInterface            $metaFactory
     */
    public function __construct(
        PublishWorkflowChecker $publishWorkflowChecker,
        DocumentManager $dm,
        TenantAwarePathBuilderInterface $pathBuilder,
        $routeBasepaths,
        MetaFactoryInterface $metaFactory
    ) {
        $this->publishWorkflowChecker = $publishWorkflowChecker;
        $this->dm = $dm;
        $this->pathBuilder = $pathBuilder;
        $this->routeBasepaths = $routeBasepaths;
        $this->metaFactory = $metaFactory;
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
     *
     * @throws \Exception
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $article = null;

        if ($responseType === LoaderInterface::SINGLE) {
            if (array_key_exists('contentPath', $parameters)) {
                $article = $this->dm->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article', $parameters['contentPath']);
            } elseif (array_key_exists('article', $parameters)) {
                $article = $parameters['article'];
            } elseif (array_key_exists('slug', $parameters)) {
                $article = $this->dm->getRepository('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article')
                    ->findOneBy(['slug' => $parameters['slug']]);
            }

            return $this->getArticleMeta($article);
        } elseif ($responseType === LoaderInterface::COLLECTION) {
            if (array_key_exists('route', $parameters)) {
                $route = $this->dm->find(null, $this->pathBuilder->build(
                    $this->routeBasepaths[0].$parameters['route']
                ));

                if ($route) {
                    $node = $this->dm->getNodeForDocument($route);
                    $identifier = $node->getIdentifier();

                    $queryStr = sprintf("SELECT * FROM [nt:unstructured] as S WHERE S.phpcr:class='SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article' AND S.route=%s", $identifier);

                    if (isset($parameters['order'])) {
                        $order = $parameters['order'];
                        if (!is_array($order) || count($order) !== 2 || (strtoupper($order[1]) != 'ASC' && strtoupper($order[1]) != 'DESC')) {
                            throw new \Exception('Order filter must have two parameters with second one asc or desc, e.g. order(id, desc)');
                        }
                        if ($order[0] === 'id') {
                            $order[0] = 'jcr:uuid';
                        } else {
                            $order[0] = mysqli_real_escape_string($order[0]);
                        }
                        $queryStr .= sprintf(' ORDER BY S.%s %s', $order[0], $order[1]);
                    }

                    $query = $this->dm->createPhpcrQuery($queryStr, QueryInterface::JCR_SQL2);

                    if (isset($parameters['limit'])) {
                        $query->setLimit($parameters['limit']);
                    }

                    if (isset($parameters['start'])) {
                        $query->setOffset($parameters['start']);
                    }

                    $articles = $this->dm->getDocumentsByPhpcrQuery($query);

                    $meta = [];
                    foreach ($articles as $article) {
                        $articleMeta = $this->getArticleMeta($article);
                        if ($articleMeta) {
                            $meta[] = $articleMeta;
                        }
                    }

                    return $meta;
                }
            }
        }

        return;
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

    private function getArticleMeta($article)
    {
        if (!is_null($article) && $this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $article)) {
            return $this->metaFactory->create($article);
        }

        return;
    }
}
