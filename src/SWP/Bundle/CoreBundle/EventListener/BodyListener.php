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
use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\Normalizer\ArrayNormalizerInterface;
use FOS\RestBundle\Normalizer\Exception\NormalizationException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class BodyListener
{
  private $decoderProvider;
  private $throwExceptionOnUnsupportedContentType;
  private $defaultFormat;
  private $arrayNormalizer;
  private $normalizeForms;

  public function __construct(
      DecoderProviderInterface $decoderProvider,
      bool $throwExceptionOnUnsupportedContentType = false,
      ArrayNormalizerInterface $arrayNormalizer = null,
      bool $normalizeForms = false
  ) {
    $this->decoderProvider = $decoderProvider;
    $this->throwExceptionOnUnsupportedContentType = $throwExceptionOnUnsupportedContentType;
    $this->arrayNormalizer = $arrayNormalizer;
    $this->normalizeForms = true;
  }

  public function setDefaultFormat(?string $defaultFormat): void
  {
    $this->defaultFormat = $defaultFormat;
  }

  public function onKernelRequest(RequestEvent $event): void
  {
    $request = $event->getRequest();

    if (!$request->attributes->get(FOSRestBundle::ZONE_ATTRIBUTE, true)) {
      return;
    }

    $method = $request->getMethod();
    $contentType = $request->headers->get('Content-Type');
    $normalizeRequest = $this->normalizeForms && $this->isFormRequest($request);

    if ($this->isDecodeable($request)) {
      $format = null === $contentType
          ? $request->getRequestFormat()
          : $request->getFormat($contentType);

      $format = $format ?: $this->defaultFormat;

      $content = $request->getContent();

      if (null === $format || !$this->decoderProvider->supports($format)) {
        if ($this->throwExceptionOnUnsupportedContentType
            && $this->isNotAnEmptyDeleteRequestWithNoSetContentType($method, $content, $contentType)
        ) {
          throw new UnsupportedMediaTypeHttpException("Request body format '$format' not supported");
        }

        return;
      }

      if (!empty($content)) {
        $decoder = $this->decoderProvider->getDecoder($format);
        $data = $decoder->decode($content);
        if (is_array($data)) {
          $request->request = new ParameterBag($data);
          $normalizeRequest = true;
        } else {
          throw new BadRequestHttpException('Invalid '.$format.' message received');
        }
      }
    }

    if (null !== $this->arrayNormalizer && $normalizeRequest) {
      $data = $request->request->all();

      try {
        $data = $this->arrayNormalizer->normalize($data);
      } catch (NormalizationException $e) {
        throw new BadRequestHttpException($e->getMessage());
      }

      $request->request = new ParameterBag($data);

      if(!empty($request->files->all())) {
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

  private function isNotAnEmptyDeleteRequestWithNoSetContentType(string $method, $content, ?string $contentType): bool
  {
    return false === ('DELETE' === $method && empty($content) && empty($contentType));
  }

  private function isDecodeable(Request $request): bool
  {
    if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
      return false;
    }

    return !$this->isFormRequest($request);
  }

  private function isFormRequest(Request $request): bool
  {
    $contentTypeParts = explode(';', $request->headers->get('Content-Type'));

    if (isset($contentTypeParts[0])) {
      return in_array($contentTypeParts[0], ['multipart/form-data', 'application/x-www-form-urlencoded']);
    }

    return false;
  }
}
