<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Transformer;

use SWP\Bundle\ContentBundle\Model\ArticleManagerInterface;
use SWP\Component\Bridge\Exception\MethodNotSupportedException;
use SWP\Component\Bridge\Exception\TransformationFailedException;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class PackageToArticleTransformer implements DataTransformerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $routeRepository;

    /**
     * @var ArticleManagerInterface
     */
    private $articleManager;

    /**
     * PackageToArticleTransformer constructor.
     *
     * @param RepositoryInterface     $routeRepository
     * @param ArticleManagerInterface $articleManager
     */
    public function __construct(RepositoryInterface $routeRepository, ArticleManagerInterface $articleManager)
    {
        $this->routeRepository = $routeRepository;
        $this->articleManager = $articleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($package)
    {
        if (!$package instanceof PackageInterface) {
            throw new TransformationFailedException(sprintf('Expected a %s!', PackageInterface::class));
        }

        // TODO replace it with article factory
        $article = new \SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article();
        $article->setParent($this->articleManager->findOneBy('content'));
        $article->setTitle($package->getHeadline());

        $article->setBody(implode('', array_map(function (ItemInterface $item) {
            return $item->getBody();
        }, $package->getItems()->toArray())));

        $article->setLocale($package->getLanguage());
        // TODO replace the hardcoded route here
        $article->setRoute($this->routeRepository->find('/swp/default/routes/news'));

        return $article;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        throw new MethodNotSupportedException('reverseTransform');
    }
}
