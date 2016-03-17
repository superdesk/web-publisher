<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\EventListener;

use SWP\Bundle\WebRendererBundle\Exception\ThemeNotFoundException;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ActiveThemeListener.
 */
class ActiveThemeListener
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ActiveThemeListener constructor.
     *
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface    $themeContext
     * @param TenantContextInterface   $tenantContext
     */
    public function __construct(
        ThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext,
        TenantContextInterface $tenantContext
    ) {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
        $this->tenantContext = $tenantContext;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws ThemeNotFoundException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $themeName = $this->tenantContext->getTenant()->getThemeName();
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()
            || $this->themeContext->getTheme()
            && $themeName === $this->themeContext->getTheme()->getName()
        ) {
            return;
        }

        $theme = $this->themeRepository->findOneByName($themeName);
        if (null === $theme) {
            throw new ThemeNotFoundException($themeName);
        }

        $this->themeContext->setTheme($theme);
    }
}
