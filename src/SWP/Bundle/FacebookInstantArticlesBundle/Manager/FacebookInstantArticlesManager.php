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
     * @return FacebookManagerInterface
     */
    public function getFacebookManager()
    {
        return $this->facebookManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageAccessToken(Facebook\Facebook $facebook, $pageId)
    {
        return $this->loopThroughPagesAndFindOneById($facebook, $this->getPagesAndTokens($facebook), $pageId);
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
