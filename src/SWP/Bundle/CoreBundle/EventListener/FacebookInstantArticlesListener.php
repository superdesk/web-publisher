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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentListBundle\Event\ContentListEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookPage;
use SWP\Bundle\CoreBundle\Repository\FacebookInstantArticlesArticleRepositoryInterface;
use SWP\Bundle\CoreBundle\Service\FacebookInstantArticlesService;
use SWP\Bundle\CoreBundle\Service\FacebookInstantArticlesServiceInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Parser\TemplateParser;
use SWP\Bundle\FacebookInstantArticlesBundle\Parser\TemplateParserInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;

final class FacebookInstantArticlesListener
{
    /**
     * @var TemplateParser
     */
    protected $templateParser;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var RepositoryInterface
     */
    protected $feedRepository;

    /**
     * @var RepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var FacebookInstantArticlesService
     */
    protected $instantArticlesService;

    /**
     * FacebookInstantArticlesListener constructor.
     */
    public function __construct(
        TemplateParserInterface $templateParser,
        MetaFactoryInterface $metaFactory,
        RepositoryInterface $feedRepository,
        RepositoryInterface $pageRepository,
        FacebookInstantArticlesServiceInterface $instantArticlesService,
        FacebookInstantArticlesArticleRepositoryInterface $facebookInstantArticlesArticleRepository
    ) {
        $this->templateParser = $templateParser;
        $this->metaFactory = $metaFactory;
        $this->feedRepository = $feedRepository;
        $this->pageRepository = $pageRepository;
        $this->instantArticlesService = $instantArticlesService;
        $this->facebookInstantArticlesArticleRepository = $facebookInstantArticlesArticleRepository;
    }

    public function sendArticleToFacebook(ContentListEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getItem()->getContent();

        if (!$article->isPublishedFBIA()) {
            return;
        }

        $feeds = $this->feedRepository->getQueryByCriteria(new Criteria([
            'contentList' => $event->getContentList(),
        ]), [], 'f')->getQuery()->getResult();
        if (0 === count($feeds)) {
            return;
        }

        $this->metaFactory->create($article);
        $instantArticle = $this->templateParser->parse();

        foreach ($feeds as $feed) {
            $this->instantArticlesService->pushInstantArticle($feed, $instantArticle, $article);
        }
    }

    public function resendUpdatedArticleToFacebook(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        if (!$article->isPublished() && !$article->isPublishedFBIA()) {
            return;
        }

        /** @var FacebookInstantArticlesArticleInterface[] $articleSubmissions */
        $articleSubmissions = $this->facebookInstantArticlesArticleRepository->findByArticle($article);
        if (0 === count($articleSubmissions)) {
            return;
        }

        $this->metaFactory->create($article);
        $instantArticle = $this->templateParser->parse();

        foreach ($articleSubmissions as $articleSubmission) {
            $this->instantArticlesService->pushInstantArticle($articleSubmission->getFeed(), $instantArticle, $article);
        }
    }

    public function removeArticleFromFacebook(ArticleEvent $event)
    {
        $article = $event->getArticle();
        $pages = $this->pageRepository->findAll();
        /** @var FacebookPage $page */
        foreach ($pages as $page) {
            $feeds = $this->feedRepository->getQueryByCriteria(new Criteria([
                'facebookPage' => $page,
            ]), [], 'f')->getQuery()->getResult();
            if (0 === count($feeds)) {
                continue;
            }

            foreach ($feeds as $feed) {
                $this->instantArticlesService->removeInstantArticle($feed, $article);
            }
        }
    }
}
