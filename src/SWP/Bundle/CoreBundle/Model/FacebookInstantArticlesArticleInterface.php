<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;

interface FacebookInstantArticlesArticleInterface extends TimestampableInterface
{
    /**
     * @return string
     */
    public function getSubmissionId(): string;

    /**
     * @return FacebookInstantArticlesFeedInterface
     */
    public function getFeed(): FacebookInstantArticlesFeedInterface;

    /**
     * @return ArticleInterface
     */
    public function getArticle(): ArticleInterface;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @param string $submissionId
     */
    public function setSubmissionId(string $submissionId);

    /**
     * @param ArticleInterface $article
     */
    public function setArticle(ArticleInterface $article);

    /**
     * @param string $status
     */
    public function setStatus(string $status);

    /**
     * @param array $errors
     */
    public function setErrors(array $errors);

    /**
     * @param FacebookInstantArticlesFeedInterface $feed
     */
    public function setFeed(FacebookInstantArticlesFeedInterface $feed);
}
