<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use FOS\RestBundle\Decoder\DecoderProviderInterface;
use FOS\RestBundle\EventListener\BodyListener as FosRestBodyListener;
use FOS\RestBundle\Normalizer\ArrayNormalizerInterface;
use FOS\RestBundle\Normalizer\Exception\NormalizationException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BodyListener extends FosRestBodyListener
{
    private $arrayNormalizer;

    public function __construct(DecoderProviderInterface $decoderProvider, $throwExceptionOnUnsupportedContentType = false, ArrayNormalizerInterface $arrayNormalizer = null, $normalizeForms = false)
    {
        $this->arrayNormalizer = $arrayNormalizer;

        parent::__construct($decoderProvider, $throwExceptionOnUnsupportedContentType, $arrayNormalizer, $normalizeForms);
    }

    public function onKernelRequest($event): void
    {
        parent::onKernelRequest($event);

        $request = $event->getRequest();

        if (null !== $this->arrayNormalizer && !$this->isDecodeable($request) && !empty($request->files->all())) {
            $data = $request->files->all();

            try {
                $data = $this->arrayNormalizer->normalize($data);
            } catch (NormalizationException $e) {
                throw new BadRequestHttpException($e->getMessage());
            }

            $request->files = new ParameterBag($data);
        }
    }
}
