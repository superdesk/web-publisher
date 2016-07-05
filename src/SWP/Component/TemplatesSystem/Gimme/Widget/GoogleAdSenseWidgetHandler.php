<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Gimme\Widget;

class GoogleAdSenseWidgetHandler extends AbstractWidgetHandler
{
    protected static $expectedParameters = [
        'ad_unit_type' => [
            'type'    => 'string',
            'default' => 'Ad unit',
        ],
        'ad_slot' => [
            'type' => 'int',
        ],
    ];

    /**
     * Render widget content.
     *
     * @return string
     */
    public function render()
    {
        // Render a template
        return '';
    }
}
