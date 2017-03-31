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

namespace SWP\Bundle\CoreBundle\Cloner;

use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\ReplaceFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyMatcher;
use DeepCopy\Matcher\PropertyNameMatcher;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Factory\ClonerFactoryInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\Image;
use SWP\Bundle\CoreBundle\Model\Organization;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;

final class ArticleCloner implements ArticleClonerInterface
{
    const PROPERTY_NAME = 'id';
    const TENANT_PROPERTY_NAME = 'tenantCode';

    /**
     * @var ClonerFactoryInterface
     */
    private $clonerFactory;

    /**
     * ArticleCloner constructor.
     *
     * @param ClonerFactoryInterface $clonerFactory
     */
    public function __construct(ClonerFactoryInterface $clonerFactory)
    {
        $this->clonerFactory = $clonerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function clone(ArticleInterface $article, array $config = []): ArticleInterface
    {
        $this->validateConfig($config);

        $tenant = $config['tenant'];
        $cloner = $this->clonerFactory->create();
        $cloner->skipUncloneable();

        $cloner->addFilter(new KeepFilter(), new PropertyTypeMatcher(Organization::class));
        $cloner->addFilter(new SetNullFilter(), new PropertyNameMatcher(self::PROPERTY_NAME));
        $cloner->addFilter(new DoctrineCollectionFilter(), new PropertyTypeMatcher(Collection::class));
        $cloner->addFilter($this->replaceFilter($tenant), new PropertyMatcher(Article::class, self::TENANT_PROPERTY_NAME));
        $cloner->addFilter($this->replaceFilter($tenant), new PropertyMatcher(ArticleMedia::class, self::TENANT_PROPERTY_NAME));
        $cloner->addFilter($this->replaceFilter($tenant), new PropertyMatcher(Image::class, self::TENANT_PROPERTY_NAME));

        $clonedArticle = $cloner->copy($article);
        $clonedArticle->setRoute($config['route']);

        return $clonedArticle;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig(array $config = [])
    {
        if (isset($config['tenant']) && !$config['tenant'] instanceof TenantInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($config['tenant']) ? get_class($config['tenant']) : gettype($config['tenant']),
                TenantInterface::class);
        }

        if (isset($config['route']) && !$config['route'] instanceof RouteInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($config['route']) ? get_class($config['route']) : gettype($config['tenant']),
                RouteInterface::class);
        }
    }

    private function replaceFilter(TenantInterface $tenant)
    {
        return new ReplaceFilter(function ($value) use ($tenant) {
            return $tenant->getCode();
        });
    }
}
