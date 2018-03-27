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

namespace SWP\Bundle\CoreBundle\Widget;

use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;

class TemplateWidgetHandler extends TemplatingWidgetHandler
{
    public const TEMPLATE_NAME = 'template_name';

    protected static $expectedParameters = [
         self::TEMPLATE_NAME => [
            'type' => 'string',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->renderTemplate($this->getModelParameter(self::TEMPLATE_NAME));
    }
}
