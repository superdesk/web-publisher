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
namespace SWP\Bundle\WebRendererBundle\Twig;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

class WebRendererExtension extends \Twig_Extension
{
    /**
     * @var ThemeContextInterface
     */
    protected $themeContext;

    /**
     * @param ThemeContextInterface $themeContext
     */
    public function __construct(ThemeContextInterface $themeContext)
    {
        $this->themeContext = $themeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'theme' => $this->themeContext->getTheme(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'swp_webrenderer';
    }
}
