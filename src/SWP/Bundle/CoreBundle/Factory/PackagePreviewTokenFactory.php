<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\PackagePreviewTokenInterface;
use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class PackagePreviewTokenFactory implements PackagePreviewTokenFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $baseFactory;

    /**
     * @var GeneratorInterface
     */
    private $randomnessGenerator;

    /**
     * PackagePreviewTokenFactory constructor.
     *
     * @param FactoryInterface   $baseFactory
     * @param GeneratorInterface $randomnessGenerator
     */
    public function __construct(FactoryInterface $baseFactory, GeneratorInterface $randomnessGenerator)
    {
        $this->baseFactory = $baseFactory;
        $this->randomnessGenerator = $randomnessGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->baseFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createTokenizedWith(RouteInterface $route, string $body): PackagePreviewTokenInterface
    {
        $packagePreviewToken = $this->create();
        $packagePreviewToken->setRoute($route);
        $packagePreviewToken->setBody($body);
        $packagePreviewToken->setToken($this->randomnessGenerator->generate(36));

        return $packagePreviewToken;
    }
}
