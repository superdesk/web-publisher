<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use Facebook\InstantArticles\Client\Client;
use Facebook\InstantArticles\Client\InstantArticleStatus;
use Facebook\InstantArticles\Elements\InstantArticle;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManagerInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Factory\FactoryInterface;

class FacebookInstantArticlesService
{
    /**
     * @var FacebookInstantArticlesManagerInterface
     */
    protected $facebookInstantArticlesManager;

    /**
     * @var FactoryInterface
     */
    protected $instantArticlesArticleFactory;

    /**
     * @var EntityRepository
     */
    protected $facebookInstantArticlesArticleRepository;

    /**
     * FacebookInstantArticlesService constructor.
     *
     * @param FacebookInstantArticlesManagerInterface $facebookInstantArticlesManager
     * @param FactoryInterface                        $instantArticlesArticleFactory
     * @param EntityRepository                        $facebookInstantArticlesArticleRepository
     */
    public function __construct(
        FacebookInstantArticlesManagerInterface $facebookInstantArticlesManager,
        FactoryInterface $instantArticlesArticleFactory,
        EntityRepository $facebookInstantArticlesArticleRepository
    ) {
        $this->facebookInstantArticlesManager = $facebookInstantArticlesManager;
        $this->instantArticlesArticleFactory = $instantArticlesArticleFactory;
        $this->facebookInstantArticlesArticleRepository = $facebookInstantArticlesArticleRepository;
    }

    /**
     * @param FacebookInstantArticlesFeedInterface $feed
     * @param InstantArticle                       $instantArticle
     * @param ArticleInterface                     $article
     */
    public function pushInstantArticle(
        FacebookInstantArticlesFeedInterface $feed,
        InstantArticle $instantArticle,
        ArticleInterface $article
    ) {
        $submissionId = $this->getClient($feed)->importArticle($instantArticle, true);

        /** @var FacebookInstantArticlesArticle $instantArticleEntity */
        $instantArticleEntity = $this->facebookInstantArticlesArticleRepository->getQueryByCriteria(new Criteria([
            'article' => $article,
            'feed' => $feed,
        ]), [], 'fbia')->getQuery()->getOneOrNullResult();

        if (null === $instantArticleEntity) {
            $instantArticleEntity = $this->instantArticlesArticleFactory->create();
            $instantArticleEntity->setArticle($article);
            $instantArticleEntity->setFeed($feed);
            $instantArticleEntity->setStatus('new');
        }

        $instantArticleEntity->setSubmissionId($submissionId);
        $this->facebookInstantArticlesArticleRepository->add($instantArticleEntity);
    }

    /**
     * @param string $submissionId
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function updateSubmissionStatus(string $submissionId)
    {
        /** @var FacebookInstantArticlesArticle $instantArticleEntity */
        $instantArticle = $this->facebookInstantArticlesArticleRepository->getQueryByCriteria(new Criteria([
            'submissionId' => $submissionId,
        ]), [], 'fbia')->getQuery()->getOneOrNullResult();

        if (null === $instantArticle) {
            throw new \Exception('Instant Article with provided submission ID does not exists.');
        }

        $submissionStatus = $this->getClient($instantArticle->getFeed())->getSubmissionStatus($submissionId);
        $instantArticle->setStatus($submissionStatus->getStatus());
        $instantArticle->setErrors($this->getSubmissionErrors($submissionStatus));
        $this->facebookInstantArticlesArticleRepository->flush();

        return $instantArticle;
    }

    /**
     * @param $feed
     *
     * @return Client
     */
    private function getClient($feed)
    {
        $facebookPage = $feed->getFacebookPage();
        $facebook = $this->facebookInstantArticlesManager->getFacebookManager()->createForApp($facebookPage->getApplication());
        $facebook->setDefaultAccessToken($facebookPage->getAccessToken());

        return new Client($facebook, $facebookPage->getPageId(), $feed->isDevelopment());
    }

    /**
     * @param InstantArticleStatus $submissionStatus
     *
     * @return array
     */
    private function getSubmissionErrors(InstantArticleStatus $submissionStatus): array
    {
        $errors = [];
        foreach ($submissionStatus->getMessages() as $serverMessage) {
            $errors[] = [$serverMessage->getLevel() => $serverMessage->getMessage()];
        }

        return $errors;
    }
}
