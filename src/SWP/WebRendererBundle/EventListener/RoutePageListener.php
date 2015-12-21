<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\WebRendererBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SWP\TemplatesSystem\Gimme\Context\Context;

class RoutePageListener
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $registryManager;

    /**
     * @var \SWP\TemplatesSystem\Gimme\Context\Context
     */
    protected $context;

    /**
     * @param Registry $registryManager
     * @param Context  $context
     */
    public function __construct(Registry $registryManager, Context $context)
    {
        $this->registryManager = $registryManager;
        $this->context = $context;
    }

    /**
     * Fill template engine context with informations about current page.
     *
     * @param GenericEvent $event
     */
    public function onRoutePage(GenericEvent $event)
    {
        $em = $this->registryManager->getManager();
        $page = $em->getRepository('SWP\ContentBundle\Model\Page')
            ->getById($event->getArguments()['pageId'])
            ->getArrayResult();

        if (count($page)) {
            $page[0]['route_name'] = $event->getArguments()['route_name'];
            $this->context->setCurrentPage($page[0]);
        }

        return;
    }
}
