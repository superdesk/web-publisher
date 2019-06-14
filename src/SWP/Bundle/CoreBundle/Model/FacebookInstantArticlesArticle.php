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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Storage\Model\PersistableInterface;

class FacebookInstantArticlesArticle implements FacebookInstantArticlesArticleInterface, PersistableInterface, TenantAwareInterface
{
    use TenantAwareTrait;
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $submissionId;

    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $errors = '{}';

    /**
     * @var FacebookInstantArticlesFeedInterface
     */
    protected $feed;

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmissionId(): string
    {
        return $this->submissionId;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubmissionId(string $submissionId)
    {
        $this->submissionId = $submissionId;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticle(ArticleInterface $article)
    {
        $this->article = $article;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return json_decode($this->errors, true);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrors(array $errors)
    {
        $this->errors = json_encode($errors);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeed(): FacebookInstantArticlesFeedInterface
    {
        return $this->feed;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeed(FacebookInstantArticlesFeedInterface $feed)
    {
        $this->feed = $feed;
    }
}
