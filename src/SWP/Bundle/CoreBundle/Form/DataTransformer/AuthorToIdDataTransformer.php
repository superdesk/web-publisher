<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\DataTransformer;

use SWP\Bundle\ContentBundle\Doctrine\ArticleAuthorRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class AuthorToIdDataTransformer implements DataTransformerInterface
{
    private $articleAuthorRepository;

    public function __construct(ArticleAuthorRepositoryInterface $articleAuthorRepository)
    {
        $this->articleAuthorRepository = $articleAuthorRepository;
    }

    public function transform($author)
    {
        if (null === $author) {
            return;
        }

        if (!$author instanceof ArticleAuthorInterface) {
            throw new UnexpectedTypeException($author, ArticleAuthorInterface::class);
        }

        return $author->getId();
    }

    public function reverseTransform($authorId)
    {
        if (null === $authorId) {
            return;
        }

        $author = $this->articleAuthorRepository->findOneBy(['id' => $authorId]);

        if (null === $author) {
            throw new TransformationFailedException(sprintf('Author with id "%s" does not exist!', $authorId));
        }

        return $author;
    }
}
