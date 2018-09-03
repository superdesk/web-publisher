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

namespace SWP\Bundle\CoreBundle\Theme\Processor;

use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;
use SWP\Bundle\CoreBundle\Theme\Generator\GeneratorInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

class RequiredDataProcessor implements RequiredDataProcessorInterface
{
    /**
     * @var RouteServiceInterface
     */
    protected $themeRoutesGenerator;

    /**
     * @var GeneratorInterface
     */
    protected $themeMenusGenerator;

    /**
     * @var GeneratorInterface
     */
    protected $themeContainersGenerator;

    /**
     * @var GeneratorInterface
     */
    protected $themeWidgetsGenerator;

    /**
     * @var GeneratorInterface
     */
    protected $themeContentListsGenerator;

    /**
     * RequiredDataProcessor constructor.
     *
     * @param GeneratorInterface $themeRoutesGenerator
     * @param GeneratorInterface $themeMenusGenerator
     * @param GeneratorInterface $themeContainersGenerator
     * @param GeneratorInterface $themeWidgetsGenerator
     * @param GeneratorInterface $themeContentListsGenerator
     */
    public function __construct(GeneratorInterface $themeRoutesGenerator, GeneratorInterface $themeMenusGenerator, GeneratorInterface $themeContainersGenerator, GeneratorInterface $themeWidgetsGenerator, GeneratorInterface $themeContentListsGenerator)
    {
        $this->themeRoutesGenerator = $themeRoutesGenerator;
        $this->themeMenusGenerator = $themeMenusGenerator;
        $this->themeContainersGenerator = $themeContainersGenerator;
        $this->themeWidgetsGenerator = $themeWidgetsGenerator;
        $this->themeContentListsGenerator = $themeContentListsGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function processTheme(ThemeInterface $theme, bool $applyOptionalData = false): void
    {
        $this->themeRoutesGenerator->generate($theme->getRoutes(), $applyOptionalData);
        $this->themeMenusGenerator->generate($theme->getMenus(), $applyOptionalData);
        $this->themeContainersGenerator->generate($theme->getContainers(), $applyOptionalData);
        $this->themeContentListsGenerator->generate($theme->getContentLists(), $applyOptionalData);
        $this->themeWidgetsGenerator->generate($theme->getWidgets(), $applyOptionalData);
    }
}
