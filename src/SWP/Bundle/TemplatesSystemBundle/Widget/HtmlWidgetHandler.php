<?php

/*
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

namespace SWP\Bundle\TemplatesSystemBundle\Widget;

/**
 * Class HtmlWidgetHandler.
 *
 * Render provided html content or pass it to selected template.
 */
class HtmlWidgetHandler extends TemplatingWidgetHandler
{
    protected static $expectedParameters = [
        'html_body' => [
            'type' => 'string',
            'default' => '',
        ],
        'template_name' => [
            'type' => 'string',
            'default' => null,
        ],
    ];

    /**
     * Render widget content.
     *
     * @return string
     */
    public function render()
    {
        $htmlBody = $this->getModelParameter('html_body');
        $templateName = $this->getModelParameter('template_name');

        if (null !== $templateName) {
            return $this->renderTemplate($templateName, ['html_body' => $htmlBody]);
        }

        return $htmlBody;
    }
}
