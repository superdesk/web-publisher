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
        'style' => [
            'type' => 'string',
            'default' => 'display:block',
        ],
        // TODO: add to client settings as two widgets on the same page probably have to have the same id
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
        $all = $this->getAllParametersWithValue();

        return $this->renderTemplate('adsense.html.twig', $all);
    }
}
