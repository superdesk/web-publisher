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

namespace SWP\Bundle\ContentBundle\Transformer;

use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Component\Bridge\Exception\MethodNotSupportedException;
use SWP\Component\Bridge\Exception\TransformationFailedException;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;

final class PackageToArticleTransformer implements DataTransformerInterface
{
    /**
     * @var ArticleFactoryInterface
     */
    private $articleFactory;

    /**
     * PackageToArticleTransformer constructor.
     *
     * @param ArticleFactoryInterface $articleFactory
     */
    public function __construct(ArticleFactoryInterface $articleFactory)
    {
        $this->articleFactory = $articleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($package)
    {
        if (!$package instanceof PackageInterface) {
            throw new TransformationFailedException(sprintf('Expected a %s!', PackageInterface::class));
        }

        return $this->articleFactory->createFromPackage($package);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        throw new MethodNotSupportedException('reverseTransform');
    }
}
