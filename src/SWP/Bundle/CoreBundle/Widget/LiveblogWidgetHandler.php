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

namespace SWP\Bundle\CoreBundle\Widget;

use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;

class LiveblogWidgetHandler extends TemplatingWidgetHandler
{
    const TEMPLATE_NAME = 'liveblog.html.twig';

    protected static $expectedParameters = [
        'url' => [
            'type' => 'string',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $url = $this->getModelParameter('url');
        dump(self::TEMPLATE_NAME, $url);

        return $this->renderTemplate(self::TEMPLATE_NAME, [
            'url' => $url,
        ]);
    }
}
