<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleManagerInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

final class ArticleManager implements ArticleManagerInterface
{
    private $objectManager;
    private $repository;
    private $pathBuilder;

    /**
     * ArticleManager constructor.
     *
     * @param ObjectManager                   $objectManager
     * @param ArticleRepositoryInterface      $repository
     * @param TenantAwarePathBuilderInterface $pathBuilder
     */
    public function __construct(
        ObjectManager $objectManager,
        ArticleRepositoryInterface $repository,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function updateArticle(ArticleInterface $article)
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($id)
    {
        return $this->repository->find($this->pathBuilder->build($id));
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenBy($path)
    {
        $children = $this->objectManager
            ->find(null, $this->pathBuilder->build($path))
            ->getChildren()
        ;

        $articles = [];
        foreach ($children as $child) {
            if ($child instanceof Article) {
                $articles[] = $child;
            }
        }

        return $articles;
    }
}
