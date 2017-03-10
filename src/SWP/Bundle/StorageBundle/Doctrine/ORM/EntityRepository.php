<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\Doctrine\ORM;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{
    use EntityRepositoryTrait;
}
