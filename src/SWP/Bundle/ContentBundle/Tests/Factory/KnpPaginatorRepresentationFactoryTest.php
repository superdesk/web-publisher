<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\Paginator;
use SWP\Component\Common\Factory\KnpPaginatorRepresentationFactory;
use SWP\Component\Common\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

class KnpPaginatorRepresentationFactoryTest extends \SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase
{
    public function testRepresentation()
    {
        $representation = new KnpPaginatorRepresentationFactory(
            PaginationInterface::PAGE_PARAMETER_NAME,
            PaginationInterface::LIMIT_PARAMETER_NAME
        );

        $paginator = new Paginator($this->getContainer()->get('event_dispatcher'));

        $pagination = $paginator->paginate(['item_1', 'item_2'], 1, 10);
        $response = $representation->createRepresentation($pagination, new Request());
        self::assertTrue(is_array($response->getInline()->getResources()));
        self::assertTrue(2 == count($response->getInline()->getResources()));

        $pagination = $paginator->paginate(new ArrayCollection(['test_item', 'test_item2', 'test_item_3']), 1, 10);
        $response = $representation->createRepresentation($pagination, new Request());
        self::assertTrue(is_array($response->getInline()->getResources()));
        self::assertTrue(3 == count($response->getInline()->getResources()));

        $pagination = $paginator->paginate(new \ArrayObject(['test_item', 'test_item2']), 1, 10);
        $response = $representation->createRepresentation($pagination, new Request());
        self::assertTrue(is_array($response->getInline()->getResources()));
        self::assertTrue(2 == count($response->getInline()->getResources()));
    }
}
