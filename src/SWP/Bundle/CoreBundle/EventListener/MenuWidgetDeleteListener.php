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
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MenuWidgetDeleteListener
{
    /**
     * @var RepositoryInterface
     */
    private $widgetRepository;

    /**
     * @param RepositoryInterface $widgetRepository
     */
    public function __construct(RepositoryInterface $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * @param GenericEvent $event
     */
    public function onMenuDeleted(GenericEvent $event)
    {
        /** @var MenuItemInterface $subject */
        if (!($subject = $event->getSubject()) instanceof MenuItemInterface) {
            throw UnexpectedTypeException::unexpectedType(get_class($subject), MenuItemInterface::class);
        }

        if (null !== $subject->getParent()) {
            return;
        }

        /** @var WidgetModelInterface $widget */
        $widget = $this->widgetRepository->findOneBy(['name' => $subject->getName()]);

        if (null !== $widget) {
            $this->widgetRepository->remove($widget);
        }
    }
}
