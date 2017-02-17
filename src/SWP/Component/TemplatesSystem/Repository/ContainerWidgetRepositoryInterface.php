<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System Component.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Repository;

use SWP\Component\Storage\Repository\RepositoryInterface;

interface ContainerWidgetRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $groupValues
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSortedWidgets(array $groupValues = []);
}
