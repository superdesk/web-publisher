<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Transformer;

use SWP\Component\Bridge\Exception\MethodNotSupportedException;
use SWP\Component\Bridge\Exception\TransformationFailedException;
use SWP\Component\Bridge\Model\Package;
use SWP\Component\Bridge\Validator\ValidatorInterface;
use SWP\Component\Common\Serializer\SerializerInterface;

final class JsonToPackageTransformer implements DataTransformerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validatorChain;

    /**
     * JsonToPackageTransformer constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validatorChain
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validatorChain)
    {
        $this->serializer = $serializer;
        $this->validatorChain = $validatorChain;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($json)
    {
        if (!$this->validatorChain->isValid($json)) {
            throw new TransformationFailedException('None of the chained validators were able to validate the data!');
        }

        $package = $this->serializer->deserialize($json, Package::class, 'json');
        // Set references
        foreach ($package->getItems() as $item) {
            $item->setPackage($package);
        }

        return $package;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        throw new MethodNotSupportedException('reverseTransform');
    }
}
