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

namespace SWP\WebRendererBundle\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Pages Loader loads routes from a Pages entries.
 *
 * You can need to configure loader in routing.yml file
 *
 * pages:
 *     type: pages
 */
class PagesLoader extends Loader
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param \Symfony\Bridge\Doctrine\ManagerRegistry$managerRegistry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $managerRegistry)
    {
        $this->em = $managerRegistry->getManager();
    }

    /**
     * Loads routes from pages in the database.
     *
     * @return RouteCollection the collection of routes stored in the database table
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        $pages = $this->em->createQuery('SELECT partial p.{id, templateName, slug, name} FROM \SWP\WebRendererBundle\Entity\Page p')->execute();
        foreach ($pages as $page) {
            $collection->add('swp_page_'.strtolower(str_replace(' ', '_', $page->getName())), new Route($page->getSlug(), [
                '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderAction',
                'page_id'     => $page->getId(),
                'template'    => $page->getTemplateName(),
            ]));
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given type (db).
     *
     * @param mixed  $resource the name of a table with title and slug field
     * @param string $type     The resource type (db)
     *
     * @return bool True if this class supports the given type (db), false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'pages' === $type;
    }
}
