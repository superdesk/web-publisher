<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Hoa\Mime\Mime;

class MediaController extends Controller
{
    /**
     * Send or render single media.
     *
     * @Route("/media/{mediaId}.{extension}", options={"expose"=true}, name="swp_media_get")
     * @Method("GET")
     */
    public function getAction(Request $request, $mediaId, $extension)
    {
        $dm = $this->container->get('swp.object_manager.media');
        $pathBuilder = $this->container->get('swp_multi_tenancy.path_builder');
        $mediaBasePath = $this->container->getParameter('swp_multi_tenancy.persistence.phpcr.media_basepath');

        $media = $dm->find(null, $pathBuilder->build($mediaBasePath).'/'.$mediaId);
        if (null == $media) {
            throw new NotFoundHttpException('Media was not found.');
        }

        $response = new Response();
        if ($media instanceof Image) {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $mediaId.'.'.$media->getFileExtension());
        } else {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $mediaId.'.'.$media->getFileExtension());
        }

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', Mime::getMimeFromExtension($media->getFileExtension()));

        $response->setPublic();
        $response->setMaxAge(63072000);
        $response->setSharedMaxAge(63072000);

        $mediaManager = $this->container->get('swp_content_bundle.manager.media');
        $response->setContent($mediaManager->getFile($media));

        return $response;
    }
}
