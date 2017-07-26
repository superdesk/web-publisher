<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Loader;

use SWP\Bundle\ContentBundle\Provider\ArticleMediaProviderInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use SWP\Bundle\ContentBundle\Loader\ArticleMediaLoader as BaseArticleMediaLoader;

class ArticleMediaLoader extends BaseArticleMediaLoader
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * ArticleMediaLoader constructor.
     *
     * @param ArticleMediaProviderInterface $articleMediaProvider
     * @param MetaFactory                   $metaFactory
     * @param Context                       $context
     * @param RequestStack                  $requestStack
     */
    public function __construct(ArticleMediaProviderInterface $articleMediaProvider, MetaFactory $metaFactory, Context $context, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        parent::__construct($articleMediaProvider, $metaFactory, $context);
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported(string $type): bool
    {
        $isPreview = $this->requestStack->getMasterRequest()->attributes->has(LoaderInterface::PREVIEW_MODE);

        return in_array($type, ['articleMedia']) && !$isPreview;
    }
}
