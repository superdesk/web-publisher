<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Provider;

use Doctrine\ORM\Query;
use SWP\Bundle\TemplatesSystemBundle\Repository\WidgetModelRepository;

class WidgetProvider implements WidgetProviderInterface
{
    /**
     * @var WidgetModelRepository
     */
    protected $widgetRepository;

    /**
     * WidgetProvider constructor.
     *
     * @param WidgetModelRepository $widgetRepository
     */
    public function __construct(WidgetModelRepository $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryForAll(): Query
    {
        return $this->widgetRepository->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById(int $id)
    {
        return $this->widgetRepository->getById($id)->getQuery()->getOneOrNullResult();
    }
}
