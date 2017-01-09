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
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;

final class ContentListWidget extends TemplatingWidgetHandler
{
    protected static $expectedParameters = [
        'list_id' => [
            'type' => 'int',
        ],
        'template_name' => [
            'type' => 'string',
            'default' => 'list.html.twig',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $templateName = $this->getModelParameter('template_name');
        $listId = (int) $this->getModelParameter('list_id');

        /** @var ContentListRepositoryInterface $contentListRepository */
        $contentListRepository = $this->getContainer()->get('swp.repository.content_list');
        /** @var ContentListInterface $contentList */
        $contentList = $contentListRepository->findListById($listId);

        return $this->renderTemplate($templateName, [
            'contentList' => $contentList,
        ]);
    }
}
