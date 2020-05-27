<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticlePreviousRelativeUrlInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RedirectingExtension extends AbstractExtension
{
    private $router;

    private $articlePreviousUrlRepository;

    public function __construct(
        RepositoryInterface $articlePreviousUrlRepository,
        RouterInterface $router
    ) {
        $this->router = $router;
        $this->articlePreviousUrlRepository = $articlePreviousUrlRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('original_url', [$this, 'getOriginalUrl']),
        ];
    }

    public function getOriginalUrl(Meta $articleMeta): ?string
    {
        if (($article = $articleMeta->getValues()) instanceof ArticleInterface) {
            /** @var ArticlePreviousRelativeUrlInterface $articleRelativeUrl */
            $articleRelativeUrl = $this->articlePreviousUrlRepository->findOneBy(
                ['article' => $article]
            );

            if (null !== $articleRelativeUrl) {
                return rtrim($this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL), '/').$articleRelativeUrl->getRelativeUrl();
            }

            return $this->router->generate(
                $article->getRoute()->getName(),
                [
                    'slug' => $article->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
    }
}
