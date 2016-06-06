<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentPushController extends FOSRestController
{
    /**
     * Adds new content to tenant content repository
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Adds new content",
     *     statusCodes={
     *         201="Returned on successful post.",
     *         204="Returned on successful delete."
     *     }
     * )
     * @Route("/api/{version}/content/push/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_push")
     * @Method("POST|DELETE")
     */
    public function pushAction(Request $request)
    {
        $parameters = $request->request->all();

        // get storage service
        // validate parameters
        // if valid add parameters to storage
        // and create Article object from it
        //
        // return new article in response.

        return new Response(json_encode([]));
    }
}
