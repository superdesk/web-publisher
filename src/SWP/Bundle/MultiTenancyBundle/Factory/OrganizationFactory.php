<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;

class OrganizationFactory implements OrganizationFactoryInterface
{
    /**
     * @var OrganizationFactoryInterface
     */
    protected $decoratedFactory;

    /**
     * @var ObjectManager
     */
    protected $documentManager;

    /**
     * @var string|null
     */
    protected $rootPath;

    /**
     * OrganizationFactory constructor.
     *
     * @param OrganizationFactoryInterface $decoratedFactory
     * @param ObjectManager                $documentManager
     * @param null                         $rootPath
     */
    public function __construct(
        OrganizationFactoryInterface $decoratedFactory,
        ObjectManager $documentManager,
        $rootPath = null
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->documentManager = $documentManager;
        $this->rootPath = $rootPath;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->decoratedFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithCode()
    {
        $organization = $this->decoratedFactory->createWithCode();

        if ($organization instanceof HierarchyInterface) {
            $organization->setParentDocument($this->documentManager->find(null, $this->rootPath));
        }

        return $organization;
    }
}
