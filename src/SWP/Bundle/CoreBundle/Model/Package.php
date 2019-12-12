<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Bridge\Model\Package as BasePackage;
use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;

class Package extends BasePackage implements PackageInterface
{
    use OrganizationAwareTrait;

    /**
     * @var ArticleInterface[]|Collection
     */
    protected $articles;

    /**
     * @var string
     */
    protected $status = ArticleInterface::STATUS_NEW;

    public function __construct()
    {
        parent::__construct();

        $this->articles = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getArticles(): Collection
    {
        if (null === $this->articles) {
            return new ArrayCollection();
        }

        return $this->articles;
    }

    /**
     * {@inheritdoc}
     */
    public function removeArticle(ArticleInterface $article)
    {
        if ($this->hasArticle($article)) {
            $article->setPackage(null);
            $this->articles->removeElement($article);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addArticle(ArticleInterface $article)
    {
        if (!$this->hasArticle($article)) {
            $article->setPackage($this);
            $this->articles->add($article);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasArticle(ArticleInterface $article)
    {
        return $this->articles->contains($article);
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
    public function getSubjectType()
    {
        return 'package';
    }
}
