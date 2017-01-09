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
use SWP\Bundle\TemplatesSystemBundle\Repository\ContainerRepository;
use SWP\Bundle\TemplatesSystemBundle\Repository\ContainerWidgetRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Class ContainerProvider.
 */
class ContainerProvider implements ContainerProviderInterface
{
    /**
     * @var ContainerRepository
     */
    protected $containerRepository;

    /**
     * @var ContainerWidgetRepository
     */
    protected $containerWidgetRepository;

    /**
     * ContainerProvider constructor.
     *
     * @param ContainerRepository       $containerRepository
     * @param ContainerWidgetRepository $containerWidgetRepository
     */
    public function __construct(ContainerRepository $containerRepository, ContainerWidgetRepository $containerWidgetRepository)
    {
        $this->containerRepository = $containerRepository;
        $this->containerWidgetRepository = $containerWidgetRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryForAll(): Query
    {
        return $this->containerRepository->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByCriteria(Criteria $criteria)
    {
        return $this->containerRepository->getQueryByCriteria($criteria, [], 'c')->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByName(string $name)
    {
        return $this->containerRepository->getByName($name)->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById(int $id)
    {
        return $this->containerRepository->getById($id)->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerWidgets(ContainerInterface $container): array
    {
        return $this->containerWidgetRepository
            ->getSortedWidgets(['container' => $container])
            ->getQuery()
            ->getResult();
    }
}
