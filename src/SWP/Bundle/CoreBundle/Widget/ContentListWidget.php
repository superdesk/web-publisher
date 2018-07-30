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

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRendererInterface;
use SWP\Bundle\TemplatesSystemBundle\Widget\TemplatingWidgetHandler;
use SWP\Component\ContentList\Model\ContentListInterface;

final class ContentListWidget extends TemplatingWidgetHandler
{
    private $loadedLists = [];

    protected static $expectedParameters = [
        'list_id' => [
            'type' => 'int',
        ],
        'list_name' => [
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
        $contentList = $this->getContentList($this->getModelParameter('list_name'), (int) $this->getModelParameter('list_id'));
        if (null === $contentList) {
            return '';
        }

        $metaFactory = $this->getContainer()->get('swp_template_engine_context.factory.meta_factory');

        return $this->renderTemplate($templateName, [
            'contentList' => $metaFactory->create($contentList),
            'listId' => $contentList->getId(),
            'listName' => $contentList->getName(),
        ]);
    }

    public function renderWidgetOpenTag(string $containerId): string
    {
        $contentList = $this->getContentList($this->getModelParameter('list_name'), (int) $this->getModelParameter('list_id'));
        if (null === $contentList) {
            return parent::renderWidgetOpenTag($containerId);
        }

        return sprintf(
            '<div id="%s_%s" class="%s" data-widget-type="contentlist" data-list-type="%s" data-list-id="%s" data-container="%s">',
            ContainerRendererInterface::WIDGET_CLASS,
            $this->widgetModel->getId(),
            ContainerRendererInterface::WIDGET_CLASS,
            $contentList->getType(),
            $contentList->getId(),
            $containerId
        );
    }

    private function getContentList(?string $listName, int $listId): ?ContentListInterface
    {
        $key = $listName.'__'.$listId;
        if (\array_key_exists($key, $this->loadedLists)) {
            return $this->loadedLists[$key];
        }

        $contentListRepository = $this->getContainer()->get('swp.repository.content_list');
        if (null !== $listName && null !== $contentList = $contentListRepository->findListByName($listName)) {
            $this->loadedLists[$key] = $contentList;

            return $contentList;
        }

        $list = $contentListRepository->findListById($listId);
        $this->loadedLists[$key] = $list;

        return $list;
    }
}
