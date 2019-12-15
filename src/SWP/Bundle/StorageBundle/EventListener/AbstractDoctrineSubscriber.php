<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú., Paweł Jędrzejewski and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú, Paweł Jędrzejewski
 * @license http://www.superdesk.org/license
 * @license http://docs.sylius.org/en/latest/contributing/code/license.html
 */

namespace SWP\Bundle\StorageBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * @author Ben Davies <ben.davies@gmail.com>
 */
abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $resources;

    /**
     * @var RuntimeReflectionService
     */
    private $reflectionService;

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    protected function isResource(ClassMetadata $metadata): bool
    {
        if (!$reflectionClass = $metadata->getReflectionClass()) {
            return false;
        }

        return $reflectionClass->implementsInterface(PersistableInterface::class);
    }

    protected function getReflectionService(): RuntimeReflectionService
    {
        if (null === $this->reflectionService) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
