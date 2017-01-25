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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MenuWidgetCreateListener
{
    /**
     * @var FactoryInterface
     */
    private $widgetFactory;

    /**
     * @var RepositoryInterface
     */
    private $widgetRepository;

    /**
     * @param FactoryInterface    $widgetFactory
     * @param RepositoryInterface $widgetRepository
     */
    public function __construct(
        FactoryInterface $widgetFactory,
        RepositoryInterface $widgetRepository
    ) {
        $this->widgetFactory = $widgetFactory;
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * @param GenericEvent $event
     */
    public function onMenuCreated(GenericEvent $event)
    {
        /** @var MenuItemInterface $subject */
        if (!($subject = $event->getSubject()) instanceof MenuItemInterface) {
            throw UnexpectedTypeException::unexpectedType(get_class($subject), MenuItemInterface::class);
        }

        if (null !== $subject->getParent()) {
            return;
        }

        /** @var WidgetModelInterface $widget */
        $widget = $this->widgetFactory->create();
        $widget->setType(WidgetModelInterface::TYPE_MENU);
        $widget->setName($subject->getName());
        $widget->setParameters([
            'menu_name' => $subject->getName(),
        ]);

        $this->widgetRepository->add($widget);
    }
}
