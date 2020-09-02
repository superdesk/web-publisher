<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\ThemeLogoUploadType;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeLogoProviderInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CurrentThemeController extends Controller
{
    /**
     * @Route("/api/{version}/theme/logo_upload/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_theme_logo_2")
     * @Route("/api/{version}/theme/logo_upload/{type}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_theme_logo")
     */
    public function uploadThemeLogoAction(Request $request, string $type = ThemeLogoProviderInterface::SETTING_NAME_DEFAULT): SingleResourceResponseInterface
    {
        $themeContext = $this->get('swp_core.theme.context.tenant_aware');

        if (null === ($theme = $themeContext->getTheme())) {
            throw new \LogicException('Theme is not set!');
        }

        $form = $this->get('form.factory')->createNamed('', ThemeLogoUploadType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
            $currentTenant = $tenantContext->getTenant();

            try {
                $settingsManager = $this->get('swp_settings.manager.settings');
                $setting = $settingsManager->get($type, ScopeContextInterface::SCOPE_THEME, $currentTenant);
                $theme->setLogoPath($setting);
                $themeLogoUploader = $this->get('swp_core.uploader.theme_logo');
                $themeLogoUploader->upload($theme);
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => 'Could not upload logo.'], new ResponseContext(400));
            }

            $settingsManager = $this->get('swp_settings.manager.settings');
            $setting = $settingsManager->set($type, $theme->getLogoPath(), ScopeContextInterface::SCOPE_THEME, $currentTenant);

            return new SingleResourceResponse($setting, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/theme/settings/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_theme_settings_list")
     */
    public function listSettingsAction(): SingleResourceResponseInterface
    {
        $themeContext = $this->get('swp_core.theme.context.tenant_aware');

        if (null === $themeContext->getTheme()) {
            throw new \LogicException('Theme is not set!');
        }

        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        $settingsManager = $this->get('swp_settings.manager.settings');
        $settings = $settingsManager->getByScopeAndOwner(ScopeContextInterface::SCOPE_THEME, $tenantContext->getTenant());

        return new SingleResourceResponse($settings);
    }
}
