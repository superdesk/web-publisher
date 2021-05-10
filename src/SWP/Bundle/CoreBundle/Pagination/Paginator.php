<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Pagination;

use Knp\Component\Pager\Paginator as BasePaginator;
use Knp\Component\Pager\Event;

class Paginator extends BasePaginator
{
    /**
     * {@inheritdoc}
     *
     * IMPORTANT: This method expects OFFSET value instead PAGE
     */
    public function paginate($target, $offset = 1, $limit = 10, array $options = array())
    {
        $limit = intval(abs($limit));
        if (!$limit) {
            throw new \LogicException('Invalid item per page number, must be a positive number');
        }

        $page = intval(round($offset / $limit), 10);
        if ($page < 1) {
            $page = 1;
        }
        $options = array_merge($this->defaultOptions, $options);

        // normalize default sort field
        if (isset($options['defaultSortFieldName']) && is_array($options['defaultSortFieldName'])) {
            $options['defaultSortFieldName'] = implode('+', $options['defaultSortFieldName']);
        }

        // default sort field and direction are set based on options (if available)
        if (!isset($_GET[$options['sortFieldParameterName']]) && isset($options['defaultSortFieldName'])) {
            $_GET[$options['sortFieldParameterName']] = $options['defaultSortFieldName'];

            if (!isset($_GET[$options['sortDirectionParameterName']])) {
                $_GET[$options['sortDirectionParameterName']] = isset($options['defaultSortDirection']) ? $options['defaultSortDirection'] : 'asc';
            }
        }

        // before pagination start
        $beforeEvent = new Event\BeforeEvent($this->eventDispatcher);
        $this->eventDispatcher->dispatch($beforeEvent, 'knp_pager.before');
        // items
        $itemsEvent = new Event\ItemsEvent($offset, $limit);
        $itemsEvent->options = &$options;
        $itemsEvent->target = &$target;
        $this->eventDispatcher->dispatch($itemsEvent, 'knp_pager.items');
        if (!$itemsEvent->isPropagationStopped()) {
            throw new \RuntimeException('One of listeners must count and slice given target');
        }
        // pagination initialization event
        $paginationEvent = new Event\PaginationEvent();
        $paginationEvent->target = &$target;
        $paginationEvent->options = &$options;
        $this->eventDispatcher->dispatch($paginationEvent, 'knp_pager.pagination');
        if (!$paginationEvent->isPropagationStopped()) {
            throw new \RuntimeException('One of listeners must create pagination view');
        }
        // pagination class can be different, with different rendering methods
        $paginationView = $paginationEvent->getPagination();
        $paginationView->setCustomParameters($itemsEvent->getCustomPaginationParameters());
        $paginationView->setCurrentPageNumber($page);
        $paginationView->setItemNumberPerPage($limit);
        $paginationView->setTotalItemCount($itemsEvent->count);
        $paginationView->setPaginatorOptions($options);
        $paginationView->setItems($itemsEvent->items);

        // after
        $afterEvent = new Event\AfterEvent($paginationView);
        $this->eventDispatcher->dispatch($afterEvent, 'knp_pager.after');

        return $paginationView;
    }
}
