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

use Knp\Component\Pager\Event;
use Knp\Component\Pager\Exception\PageNumberOutOfRangeException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator implements PaginatorInterface {
  private EventDispatcherInterface $eventDispatcher;
  private ?RequestStack $requestStack;

  private array $defaultOptions = [
      self::PAGE_PARAMETER_NAME => 'page',
      self::SORT_FIELD_PARAMETER_NAME => 'sort',
      self::SORT_DIRECTION_PARAMETER_NAME => 'direction',
      self::FILTER_FIELD_PARAMETER_NAME => 'filterParam',
      self::FILTER_VALUE_PARAMETER_NAME => 'filterValue',
      self::DISTINCT => true,
      self::PAGE_OUT_OF_RANGE => self::PAGE_OUT_OF_RANGE_IGNORE,
      self::DEFAULT_LIMIT => self::DEFAULT_LIMIT_VALUE,
  ];


  public function __construct(EventDispatcherInterface $eventDispatcher, ?RequestStack $requestStack = null) {
    $this->eventDispatcher = $eventDispatcher;
    $this->requestStack = $requestStack;
  }

  public function setDefaultPaginatorOptions(array $options): void {
    $this->defaultOptions = \array_merge($this->defaultOptions, $options);
  }

  /**
   * {@inheritdoc}
   *
   * IMPORTANT: This method expects OFFSET value instead PAGE
   */
  public function paginate($target, int $offset = 1, $limit = 10, array $options = array()) : PaginationInterface {
    $limit = intval(abs($limit));
    if (!$limit) {
      throw new \LogicException('Invalid item per page number, must be a positive number');
    }

    $page = intval(round($offset / $limit), 10);
    if ($page < 1) {
      $page = 1;
    }

    $options = \array_merge($this->defaultOptions, $options);

    // normalize default sort field
    if (isset($options[PaginatorInterface::DEFAULT_SORT_FIELD_NAME]) && is_array($options[PaginatorInterface::DEFAULT_SORT_FIELD_NAME])) {
      $options[PaginatorInterface::DEFAULT_SORT_FIELD_NAME] = implode('+', $options[PaginatorInterface::DEFAULT_SORT_FIELD_NAME]);
    }

    $request = null === $this->requestStack ? Request::createFromGlobals() : $this->requestStack->getCurrentRequest();

    // default sort field and direction are set based on options (if available)
    if (isset($options[self::DEFAULT_SORT_FIELD_NAME]) && !$request->query->has($options[self::SORT_FIELD_PARAMETER_NAME])) {
      $request->query->set($options[self::SORT_FIELD_PARAMETER_NAME], $options[self::DEFAULT_SORT_FIELD_NAME]);

      if (!$request->query->has($options[PaginatorInterface::SORT_DIRECTION_PARAMETER_NAME])) {
        $request->query->set($options[PaginatorInterface::SORT_DIRECTION_PARAMETER_NAME], $options[PaginatorInterface::DEFAULT_SORT_DIRECTION] ?? 'asc');
      }
    }

    // before pagination start
    $beforeEvent = new Event\BeforeEvent($this->eventDispatcher, $request);
    $this->eventDispatcher->dispatch($beforeEvent, 'knp_pager.before');
    // items
    $itemsEvent = new Event\ItemsEvent($offset, $limit);
    $itemsEvent->options = &$options;
    $itemsEvent->target = &$target;
    $this->eventDispatcher->dispatch($itemsEvent, 'knp_pager.items');
    if (!$itemsEvent->isPropagationStopped()) {
      throw new \RuntimeException('One of listeners must count and slice given target');
    }
    if ($page > ceil($itemsEvent->count / $limit)) {
      $pageOutOfRangeOption = $options[PaginatorInterface::PAGE_OUT_OF_RANGE] ?? $this->defaultOptions[PaginatorInterface::PAGE_OUT_OF_RANGE];
      if ($pageOutOfRangeOption === PaginatorInterface::PAGE_OUT_OF_RANGE_FIX && $itemsEvent->count > 0) {
        // replace page number out of range with max page
        return $this->paginate($target, (int) ceil($itemsEvent->count / $limit), $limit, $options);
      }
      if ($pageOutOfRangeOption === self::PAGE_OUT_OF_RANGE_THROW_EXCEPTION && $page > 1) {
        throw new PageNumberOutOfRangeException(
            sprintf('Page number: %d is out of range.', $page),
            (int) ceil($itemsEvent->count / $limit)
        );
      }
    }

    // pagination initialization event
    $paginationEvent = new Event\PaginationEvent;
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
