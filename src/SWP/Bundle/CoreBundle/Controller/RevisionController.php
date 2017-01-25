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
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Revision\Model\RevisionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RevisionController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Show current revision",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_single", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param int $id
     *
     * @return SingleResourceResponse
     */
    public function getSingleAction($id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Show current revision",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/current", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_current")
     * @Method("GET")
     *
     * @return SingleResourceResponse
     */
    public function getCurrentAction()
    {
        $revisionContext = $this->get('swp_revision.context.revision');

        return new SingleResourceResponse($revisionContext->getCurrentRevision());
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Show all tenant revisions",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/all", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_all")
     * @Method("GET")
     *
     * @return SingleResourceResponse
     */
    public function getRevisionsAction()
    {
        return new SingleResourceResponse($this->get('swp_revision.context.revision'));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lock current revision on working",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/lock/working", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_lock_working")
     * @Method("POST")
     *
     * @return Response
     */
    public function lockWorkingRevisionAction()
    {
        $revisionContext = $this->get('swp_revision.context.revision');
        $response = new Response();
        $response->headers->setCookie(new Cookie('swp_revision_key', $revisionContext->getWorkingRevision()->getUniqueKey()));

        return $response;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Publish working revision",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/publish", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_publish")
     * @Method("POST")
     *
     * @return SingleResourceResponse
     */
    public function publishAction()
    {
        $revisionManager = $this->get('swp.manager.revision');
        $revision = $this->get('swp_revision.context.revision')->getWorkingRevision();
        $revisionManager->publish($revision);

        return new SingleResourceResponse($revision);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Un;ock working revision",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/revision/unlock/working", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_revision_unlock_working")
     * @Method("POST")
     *
     * @return Response
     */
    public function unlockWorkingRevisionAction()
    {
        $response = new Response();
        $response->headers->clearCookie('swp_revision_key');

        return $response;
    }

    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return null|RevisionInterface
     */
    private function findOr404($id)
    {
        if (null === $revision = $this->get('swp.repository.revision')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('Revision was not found.');
        }

        return $revision;
    }
}
