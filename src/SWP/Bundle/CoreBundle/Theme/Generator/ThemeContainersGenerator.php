<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Generator;

use SWP\Bundle\TemplatesSystemBundle\Form\Type\ContainerType;
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerServiceInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ThemeContainersGenerator implements GeneratorInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FactoryInterface
     */
    protected $containerFactory;

    /**
     * @var RepositoryInterface
     */
    protected $containerRepository;

    /**
     * @var ContainerServiceInterface
     */
    protected $containerService;

    /**
     * ThemeContainersGenerator constructor.
     *
     * @param FormFactoryInterface      $formFactory
     * @param FactoryInterface          $containerFactory
     * @param RepositoryInterface       $containerRepository
     * @param ContainerServiceInterface $containerService
     */
    public function __construct(FormFactoryInterface $formFactory, FactoryInterface $containerFactory, RepositoryInterface $containerRepository, ContainerServiceInterface $containerService)
    {
        $this->formFactory = $formFactory;
        $this->containerFactory = $containerFactory;
        $this->containerRepository = $containerRepository;
        $this->containerService = $containerService;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $containers, bool $applyOptionalData): void
    {
        foreach ($containers as $containerData) {
            if (null !== $this->containerRepository->findOneByName($containerData['name'])) {
                continue;
            }

            $this->containerService->createContainer($containerData['name'], [], $this->createContainer($containerData));
        }
    }

    /**
     * @param array $containerData
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createContainer(array $containerData)
    {
        $container = $this->containerFactory->create();
        $form = $this->formFactory->create(ContainerType::class, $container);
        $form->submit($containerData, false);
        if (!$form->isValid()) {
            throw new \Exception('Invalid container definition');
        }

        return $container;
    }
}
