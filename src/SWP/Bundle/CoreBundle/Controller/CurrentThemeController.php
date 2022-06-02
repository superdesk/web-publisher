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

use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\ThemeLogoUploadType;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeLogoProviderInterface;
use SWP\Bundle\CoreBundle\Theme\TenantAwareThemeContextInterface;
use SWP\Bundle\CoreBundle\Theme\Uploader\ThemeLogoUploaderInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CurrentThemeController extends AbstractController {

  private TenantAwareThemeContextInterface $tenantAwareThemeContext;
  private FormFactoryInterface $formFactory;
  private CachedTenantContextInterface $tenantContext;
  private SettingsManagerInterface $settingsManager;
  private ThemeLogoUploaderInterface $themeLogoUploader;

  /**
   * @param TenantAwareThemeContextInterface $tenantAwareThemeContext
   * @param FormFactoryInterface $formFactory
   * @param CachedTenantContextInterface $tenantContext
   * @param SettingsManagerInterface $settingsManager
   * @param ThemeLogoUploaderInterface $themeLogoUploader
   */
  public function __construct(TenantAwareThemeContextInterface $tenantAwareThemeContext,
                              FormFactoryInterface             $formFactory,
                              CachedTenantContextInterface     $tenantContext,
                              SettingsManagerInterface         $settingsManager,
                              ThemeLogoUploaderInterface       $themeLogoUploader) {
    $this->tenantAwareThemeContext = $tenantAwareThemeContext;
    $this->formFactory = $formFactory;
    $this->tenantContext = $tenantContext;
    $this->settingsManager = $settingsManager;
    $this->themeLogoUploader = $themeLogoUploader;
  }


  /**
   * @Route("/api/{version}/theme/logo_upload/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_theme_logo_2")
   * @Route("/api/{version}/theme/logo_upload/{type}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_theme_logo")
   */
  public function uploadThemeLogoAction(Request $request,
                                        string  $type = ThemeLogoProviderInterface::SETTING_NAME_DEFAULT): SingleResourceResponseInterface {
    $themeContext = $this->tenantAwareThemeContext;

    if (null === ($theme = $themeContext->getTheme())) {
      throw new \LogicException('Theme is not set!');
    }

    $form = $this->formFactory->createNamed('', ThemeLogoUploadType::class, $theme);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $tenantContext = $this->tenantContext;
      $currentTenant = $tenantContext->getTenant();

      try {
        $settingsManager = $this->settingsManager;
        $setting = $settingsManager->get($type, ScopeContextInterface::SCOPE_THEME, $currentTenant);
        $theme->setLogoPath($setting);
        $themeLogoUploader = $this->themeLogoUploader;
        $themeLogoUploader->upload($theme);
      } catch (\Exception $e) {
        return new SingleResourceResponse(['message' => 'Could not upload logo.'], new ResponseContext(400));
      }

      $settingsManager = $this->settingsManager;
      $setting = $settingsManager->set($type, $theme->getLogoPath(), ScopeContextInterface::SCOPE_THEME, $currentTenant);

      return new SingleResourceResponse($setting, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/theme/settings/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_theme_settings_list")
   */
  public function listSettingsAction(): SingleResourceResponseInterface {
    $themeContext = $this->tenantAwareThemeContext;

    if (null === $themeContext->getTheme()) {
      throw new \LogicException('Theme is not set!');
    }

    $tenantContext = $this->tenantContext;
    $settingsManager = $this->settingsManager;
    $settings = $settingsManager->getByScopeAndOwner(ScopeContextInterface::SCOPE_THEME, $tenantContext->getTenant());

    return new SingleResourceResponse($settings);
  }
}
