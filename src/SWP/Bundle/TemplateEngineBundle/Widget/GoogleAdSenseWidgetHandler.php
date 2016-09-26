<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplateEngineBundle\Widget;

class GoogleAdSenseWidgetHandler extends TemplatingWidgetHandler
{
    protected static $expectedParameters = [
        'style' => [
            'type' => 'string',
            'default' => 'display:block',
        ],
        'ad_client' => [
            'type' => 'string',
        ],
        'ad_test' => [
            'type' => 'string',
            'default' => 'off',
        ],
        'ad_slot' => [
            'type' => 'int',
        ],
        'ad_format' => [
            'type' => 'string',
            'default' => 'auto',
        ],
    ];

    /**
     * Render widget content.
     *
     * @return string
     */
    public function render()
    {
        return $this->renderTemplate('adsense.html.twig');
    }
}
