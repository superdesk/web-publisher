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

    /**
     * AbstractDoctrineSubscriber constructor.
     *
     * @param array $resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return bool
     */
    protected function isResource(ClassMetadata $metadata)
    {
        if (!$reflClass = $metadata->getReflectionClass()) {
            return false;
        }

        return $reflClass->implementsInterface(PersistableInterface::class);
    }

    /**
     * @return RuntimeReflectionService
     */
    protected function getReflectionService()
    {
        if (null === $this->reflectionService) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
