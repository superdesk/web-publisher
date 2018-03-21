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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;

final class ArticlePreviewTemplateHelper implements ArticlePreviewTemplateHelperInterface
{
    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * ArticlePreviewTemplateHelper constructor.
     *
     * @param MetaFactoryInterface $metaFactory
     * @param Context              $context
     */
    public function __construct(MetaFactoryInterface $metaFactory, Context $context)
    {
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function enablePreview(ArticleInterface $article): void
    {
        $this->context->setPreviewMode(true);
        $this->context->setCurrentPage($this->metaFactory->create($article->getRoute()));
        $this->context->getMetaForValue($article);
    }
}
