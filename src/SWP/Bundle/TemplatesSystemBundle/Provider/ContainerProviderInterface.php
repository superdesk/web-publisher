<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System Bundle.
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
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

interface ContainerProviderInterface
{
    /**
     * @return Query
     */
    public function getQueryForAll(): Query;

    /**
     * @param Criteria $criteria
     *
     * @return null|ContainerInterface
     */
    public function getOneByCriteria(Criteria $criteria);

    /**
     * @param string $name
     *
     * @return null|ContainerInterface
     */
    public function getOneByName(string $name);

    /**
     * @param int $id
     *
     * @return null|ContainerInterface
     */
    public function getOneById(int $id);

    /**
     * @param ContainerInterface $container
     *
     * @return array
     */
    public function getContainerWidgets(ContainerInterface $container): array;
}
