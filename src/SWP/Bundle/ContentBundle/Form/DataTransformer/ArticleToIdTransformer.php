<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Form\DataTransformer;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class ArticleToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ArticleProviderInterface
     */
    private $articleProvider;

    /**
     * ArticleToIdTransformer constructor.
     *
     * @param ArticleProviderInterface $articleProvider
     */
    public function __construct(ArticleProviderInterface $articleProvider)
    {
        $this->articleProvider = $articleProvider;
    }

    /**
     * Transforms an object (article) to a string (id).
     *
     * @param ArticleInterface|string $article
     *
     * @return string
     */
    public function transform($article)
    {
        if (null === $article) {
            return;
        }

        if (!$article instanceof ArticleInterface) {
            throw new UnexpectedTypeException($article, ArticleInterface::class);
        }

        return $article->getId();
    }

    /**
     * Transforms an id to an object (article).
     *
     * @param string $articleId
     *
     * @return ArticleInterface|void
     *
     * @throws TransformationFailedException if object (article) is not found
     */
    public function reverseTransform($articleId)
    {
        if (null === $articleId) {
            return;
        }

        $article = $this->articleProvider->getOneById($articleId);

        if (null === $article) {
            throw new TransformationFailedException(sprintf(
                'Article with id "%s" does not exist!',
                $article
            ));
        }

        return $article;
    }
}
