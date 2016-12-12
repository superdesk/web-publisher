<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Manager;

use Facebook;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use Facebook\InstantArticles\Client\Client;

class FacebookInstantArticlesManager implements FacebookInstantArticlesManagerInterface
{
    /**
     * @var FacebookManagerInterface
     */
    protected $facebookManager;

    /**
     * FacebookInstantArticlesManager constructor.
     *
     * @param FacebookManagerInterface $facebookManager
     */
    public function __construct(FacebookManagerInterface $facebookManager)
    {
        $this->facebookManager = $facebookManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageAccessToken(Facebook\Facebook $facebook, $pageId)
    {
        return $this->loopThroughPagesAndFindOneById($facebook, $this->getPagesAndTokens($facebook), $pageId);
    }

    public function sendArticleToFacebook(FacebookInstantArticlesFeedInterface $feed, Facebook\InstantArticles\Elements\InstantArticleInterface $article)
    {
        $client = $this->getClient($feed);
        $submissionId = $client->importArticle($article, true);

        return $submissionId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmissionStatus(FacebookInstantArticlesFeedInterface $feed, string $submissionId): Facebook\InstantArticles\Client\InstantArticleStatus
    {
        $client = $this->getClient($feed);

        return $client->getSubmissionStatus($submissionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPagesAndTokens(Facebook\Facebook $facebook)
    {
        $userAccessToken = $facebook->getRedirectLoginHelper()->getAccessToken();
        $helper = new Facebook\InstantArticles\Client\Helper($facebook);

        return $helper->getPagesAndTokens($userAccessToken);
    }

    private function getClient($feed)
    {
        $facebook = $this->facebookManager->createForApp($feed->getFacebookPage()->getApplication());
        $facebook->setDefaultAccessToken($feed->getFacebookPage()->getAccessToken());

        return new Client($facebook, $feed->getFacebookPage()->getPageId(), $feed->isDevelopment());
    }

    /**
     * @param Facebook\Facebook             $facebook
     * @param Facebook\GraphNodes\GraphEdge $pagesAndAccessTokens
     * @param string                        $pageId
     *
     * @return string|null
     */
    private function loopThroughPagesAndFindOneById($facebook, $pagesAndAccessTokens, $pageId)
    {
        foreach ($pagesAndAccessTokens as $page) {
            /** @var Facebook\GraphNodes\GraphNode $page */
            $page = $page->asArray();

            if ($page['id'] == $pageId) {
                return $page['access_token'];
            }
        }

        if (null !== $nextPagesAndAccessTokens = $facebook->next($pagesAndAccessTokens)) {
            return $this->loopThroughPagesAndFindOneById($facebook, $nextPagesAndAccessTokens, $pageId);
        }

        return;
    }
}
