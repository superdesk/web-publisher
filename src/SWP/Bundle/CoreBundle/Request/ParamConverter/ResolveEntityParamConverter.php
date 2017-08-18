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

namespace SWP\Bundle\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\CoreBundle\Processor\ArticleBodyProcessorInterface;
use Takeit\Bundle\AmpHtmlBundle\Request\ParamConverter\ResolveEntityParamConverter as BaseResolveEntityParamConverter;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResolveEntityParamConverter.
 */
class ResolveEntityParamConverter extends BaseResolveEntityParamConverter
{
    /**
     * @var RepositoryInterface
     */
    protected $packageRepository;

    /**
     * @var Context
     */
    protected $templateContext;

    /**
     * @var ArticleBodyProcessorInterface
     */
    protected $articleBodyProcessor;

    /**
     * @var ArticleFactoryInterface
     */
    protected $articleFactory;

    /**
     * @var MediaFactoryInterface
     */
    protected $mediaFactory;

    /**
     * @var array
     */
    protected $mapping;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * ResolveEntityParamConverter constructor.
     *
     * @param array                         $mapping
     * @param Context                       $templateContext
     * @param ArticleBodyProcessorInterface $articleBodyProcessor
     * @param RepositoryInterface           $packageRepository
     * @param ArticleFactoryInterface       $articleFactory
     * @param MediaFactoryInterface         $mediaFactory
     * @param ManagerRegistry|null          $registry
     */
    public function __construct(
        array $mapping,
        Context $templateContext,
        ArticleBodyProcessorInterface $articleBodyProcessor,
        RepositoryInterface $packageRepository,
        ArticleFactoryInterface $articleFactory,
        MediaFactoryInterface $mediaFactory,
        ManagerRegistry $registry = null
    ) {
        $this->mapping = $mapping;
        $this->packageRepository = $packageRepository;
        $this->templateContext = $templateContext;
        $this->articleBodyProcessor = $articleBodyProcessor;
        $this->articleFactory = $articleFactory;
        $this->mediaFactory = $mediaFactory;
        $this->registry = $registry;

        parent::__construct($mapping, $registry);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $options = $this->getOptions($configuration);
        $id = $this->getIdentifier($request, $options, $name);
        if (false === $id || null === $id) {
            return false;
        }

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        /** @var PackageInterface $package */
        $package = $this->findPackageOr404((int) $id);
        /** @var ArticleInterface $article */
        $article = $this->articleFactory->createFromPackage($package);
        $this->articleBodyProcessor->fillArticleMedia($this->mediaFactory, $package, $article);
        $article->setId($id);

        $request->attributes->set($name, $article);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (!$this->templateContext->isPreviewMode()) {
            return false;
        }

        return parent::supports($configuration);
    }

    /**
     * @param int $id
     *
     * @return null|object
     */
    private function findPackageOr404(int $id)
    {
        if (null === ($package = $this->packageRepository->findOneBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('Package with id: "%s" not found!', $id));
        }

        return $package;
    }
}
