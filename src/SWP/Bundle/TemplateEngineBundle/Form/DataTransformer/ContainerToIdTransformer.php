<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\TemplateEngineBundle\Model\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContainerToIdTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Container $container
     * @return string
     */
    public function transform($container)
    {
        if (null === $container) {
            return '';
        }

        return $container->getId();
    }

    /**
     * @param string $containerName
     */
    public function reverseTransform($id)
    {
        if (null === $id) {
            return;
        }

        $container = $this->manager
            ->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')
            ->find($id);

        if (null === $container) {
            throw new TransformationFailedException(sprintf(
                'A container with name %s does not exist',
                $id
            ));
        }

        return $container;
    }
}
