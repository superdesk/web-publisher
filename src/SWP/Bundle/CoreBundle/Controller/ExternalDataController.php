<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Superdesk\ContentApiSdk\Exception\InvalidDataException;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExternalDataController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Set new package external data",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     }
     * )
     * @Route("/api/{version}/packages/extra/{slug}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_add_extra_data")
     * @Method("PUT")
     */
    public function setAction(Request $request, string $slug)
    {
        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->get('swp.repository.package');
        /** @var PackageInterface $existingPackage */
        $existingPackage = $packageRepository->findOneBy(['slugline' => $slug]);
        if (null === $existingPackage) {
            throw new NotFoundHttpException(sprintf('package with slug %s was not found', $slug));
        }

        $externalData = $existingPackage->getExternalData();
        if (null === $externalData) {
            $externalData = $this->get('swp.factory.external_data')->create();
        }

        if (null !== $request->getContent()) {
            $validJson = self::getArrayFromJson($request->getContent());
            $externalData->setData($validJson);
            $existingPackage->setExternalData($externalData);
            $packageRepository->flush();

            return new SingleResourceResponse($externalData->getData(), new ResponseContext(200));
        }

        return new SingleResourceResponse(['Provided request content is not valid JSON'], new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get package external data",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     },
     * )
     * @Route("/api/{version}/packages/extra/{slug}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_get_extra_data")
     * @Method("GET")
     */
    public function getAction(string $slug)
    {
        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->get('swp.repository.package');
        /** @var PackageInterface $existingPackage */
        $existingPackage = $packageRepository->findOneBy(['slugline' => $slug]);
        if (null === $existingPackage) {
            throw new NotFoundHttpException(sprintf('package with slug %s was not found', $slug));
        }

        $externalData = $existingPackage->getExternalData();
        if (null === $externalData) {
            return new SingleResourceResponse([], new ResponseContext(200));
        }

        return new SingleResourceResponse($externalData->getData(), new ResponseContext(200));
    }

    private static function getArrayFromJson($jsonString)
    {
        $jsonArray = json_decode($jsonString, true);
        if (is_null($jsonArray) || JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidDataException('Provided request content is not valid JSON');
        }

        return $jsonArray;
    }
}
