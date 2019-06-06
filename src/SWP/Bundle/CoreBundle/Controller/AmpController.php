<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\Common\Cache\CacheProvider;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Takeit\Bundle\AmpHtmlBundle\Converter\AmpConverterInterface;
use Takeit\Bundle\AmpHtmlBundle\Loader\ThemeLoaderInterface;
use Takeit\Bundle\AmpHtmlBundle\Model\AmpInterface;
use Twig\Environment;

final class AmpController extends AbstractController
{
    private $twig;

    private $converter;

    private $themeLoader;

    private $cacheService;

    public function __construct(
        Environment $twig,
        AmpConverterInterface $ampConverter,
        ThemeLoaderInterface $ampThemeLoader,
        CacheProvider $cacheService
    ) {
        $this->twig = $twig;
        $this->converter = $ampConverter;
        $this->themeLoader = $ampThemeLoader;
        $this->cacheService = $cacheService;
    }

    public function viewAction(AmpInterface $object): Response
    {
        if ($this->cacheService->contains($cacheKey = $this->getCacheKey($object))) {
            return $this->cacheService->fetch($cacheKey);
        }

        $this->themeLoader->load();
        $content = $this->twig->render(sprintf('@%s/index.html.twig', ThemeLoaderInterface::THEME_NAMESPACE), [
            'object' => $object,
        ]);

        $response = new Response();
        $response->setContent($this->converter->convertToAmp($content));

        $this->cacheService->save($cacheKey, $response);

        return $response;
    }

    private function getCacheKey(AmpInterface $object): string
    {
        $elements = ['amp_article'];
        if ($object instanceof PersistableInterface) {
            $elements[] = $object->getId();
        }

        if ($object instanceof TimestampableInterface && null !== $object->getUpdatedAt()) {
            $elements[] = $object->getUpdatedAt()->getTimestamp();
        }

        return md5(implode('__', $elements));
    }
}
