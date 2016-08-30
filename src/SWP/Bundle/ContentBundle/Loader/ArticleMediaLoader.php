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

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Doctrine\ODM\PHPCR\DocumentManager;

/**
 * Class ArticleMediaLoader.
 */
class ArticleMediaLoader implements LoaderInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * ArticleMediaLoader constructor.
     *
     * @param DocumentManager $dm
     * @param MetaFactory     $metaFactory
     * @param Context         $context
     */
    public function __construct(
        DocumentManager $dm,
        MetaFactory $metaFactory,
        Context $context
    ) {
        $this->dm = $dm;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Media Loader loads article media from Content Repository",
     *     parameters={
     *         article="COLLECTION| article Meta object"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: collection of meta (LoaderInterface::COLLECTION)
     *
     * @return Meta[]|bool false if meta cannot be loaded, an array with Meta instances otherwise
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::COLLECTION)
    {
        if ($responseType === LoaderInterface::COLLECTION) {
            $media = false;
            if (array_key_exists('article', $parameters)) {
                $media = $this->dm->find(null, $parameters['article']->getValues()->getId().'/'.ArticleMediaInterface::PATH_MEDIA);
            } elseif (null !== $this->context->article) {
                $media = $this->dm->find(null, $this->context->article->getValues()->getId().'/'.ArticleMediaInterface::PATH_MEDIA);
            }

            if ($media) {
                $meta = [];
                foreach ($media->getChildren() as $item) {
                    $meta[] = $this->metaFactory->create($item);
                }

                return $meta;
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
        return in_array($type, ['articleMedia']);
    }
}
