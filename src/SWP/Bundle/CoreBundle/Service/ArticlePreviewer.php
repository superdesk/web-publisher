<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Processor\ArticleBodyProcessorInterface;
use SWP\Component\Common\Exception\NotFoundHttpException;

final class ArticlePreviewer implements ArticlePreviewerInterface
{
    /**
     * @var ArticleBodyProcessorInterface
     */
    private $articleBodyProcessor;

    /**
     * @var ArticleFactoryInterface
     */
    private $articleFactory;

    /**
     * @var ArticlePreviewTemplateHelperInterface
     */
    private $articlePreviewHelper;

    /**
     * ArticlePreviewer constructor.
     *
     * @param ArticleFactoryInterface               $articleFactory
     * @param ArticleBodyProcessorInterface         $articleBodyProcessor
     * @param ArticlePreviewTemplateHelperInterface $articlePreviewHelper
     */
    public function __construct(
        ArticleFactoryInterface $articleFactory,
        ArticleBodyProcessorInterface $articleBodyProcessor,
        ArticlePreviewTemplateHelperInterface $articlePreviewHelper
    ) {
        $this->articleFactory = $articleFactory;
        $this->articleBodyProcessor = $articleBodyProcessor;
        $this->articlePreviewHelper = $articlePreviewHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function preview(PackageInterface $package, RouteInterface $route): ArticleInterface
    {
        $article = $this->articleFactory->createFromPackage($package);
        $this->articleBodyProcessor->fillArticleMedia($package, $article);
        $article->setRoute($route);

        if (null === $article->getRoute()) {
            throw new NotFoundHttpException('There is no route set!');
        }

        $this->articlePreviewHelper->enablePreview($article);

        return $article;
    }
}
