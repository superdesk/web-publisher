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

/**
 * Widgets idea
 * * Every widget have it's own clas with widget implementation
 * * Every widget have his own parameters.
 */
class HtmlWidgetHandler extends AbstractWidgetHandler
{
    protected static $expectedParameters = [
        'html_body' => [
            'type' => 'string',
            'default' => '',
        ],
    ];

    /**
     * Render widget content.
     *
     * @return string
     */
    public function render()
    {
        return $this->getModelParameter('html_body');
    }
}
