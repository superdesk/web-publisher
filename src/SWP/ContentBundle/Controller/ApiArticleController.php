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

namespace SWP\ContentBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\ContentBundle\Do*cument\Article;

class ApiArticleController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Gets an Article",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Returned when Article could not be found."
     *     }
     * )
     * @Rest\View
     */
    public function getAction($id)
    {

    }

    /**
     * @ApiDoc(
     *  resource=true,
     *     description="Creates an Article and returns reference url.",
     *     statusCodes={
     *         201="Returned when Article was created successfully.",
     *         404="Returned when Article could not be found.",
     *         409="Returned when Article already exists."
     *     }
     * )
     * @Rest\View
     */
    public function newAction()
    {

    }

    /**
     * @ApiDoc(
     *  resource=true,
     *     description="Updates an Article.",
     *     statusCodes={
     *         204="Returned when Article was updated successfully.",
     *         404="Returned when Article could not be found."
     *     }
     * )
     * @Rest\View
     */
    public function editAction($article)
    {

    }

    /**
     * @ApiDoc(
     *  resource=true,
     *     description="Deletes an Article.",
     *     statusCodes={
     *         200="Returned when Article was removed successfully.",
     *         404="Returned when Article could not be found."
     *     }
     * )
     * @Rest\View
     */
    public function deleteAction($id)
    {

    }
}
