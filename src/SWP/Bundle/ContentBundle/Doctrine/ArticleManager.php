<?php

namespace SWP\Bundle\ContentBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\Phpcr\Article;
use SWP\Bundle\ContentBundle\Model\AbstractManager;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleManagerInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

final class ArticleManager extends AbstractManager implements ArticleManagerInterface
{
    private $objectManager;
    private $repository;
    private $class;
    private $pathBuilder;

    public function __construct(
        ObjectManager $objectManager,
        ArticleRepositoryInterface $repository,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->class = $repository->getClassName();
        $this->pathBuilder = $pathBuilder;
    }

    public function updateArticle(ArticleInterface $article)
    {
        $this->objectManager->flush();
    }

    public function getObjectClass()
    {
        return $this->class;
    }

    public function findOneBy($id)
    {
        $this->repository->find($this->pathBuilder->build($id));
    }

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
