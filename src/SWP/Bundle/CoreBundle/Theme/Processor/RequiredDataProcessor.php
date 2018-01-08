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
     * RequiredDataProcessor constructor.
     *
     * @param GeneratorInterface $themeRoutesGenerator
     * @param GeneratorInterface $themeMenusGenerator
     */
    public function __construct(GeneratorInterface $themeRoutesGenerator, GeneratorInterface $themeMenusGenerator)
    {
        $this->themeRoutesGenerator = $themeRoutesGenerator;
        $this->themeMenusGenerator = $themeMenusGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function processTheme(ThemeInterface $theme): void
    {
        $this->themeRoutesGenerator->generate($theme->getRoutes());
        $this->themeMenusGenerator->generate($theme->getMenus());
    }
}
