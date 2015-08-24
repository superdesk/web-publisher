<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\UpdaterBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

class UpdaterController extends FOSRestController
{
    /**
     * Downloads all available updates to the server on which current
     * app instance is running. Downloaded update packages, by default will be saved
     * to 'app/cache/{env}' directory, until defined differently in bundle config.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Downloads updates",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Returned when file could not be found at specified url."
     *     }
     * )
     * @Route("/api/updates/download/{resource}", options={"expose"=true})
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function downloadAction($resource)
    {
        $updater = $this->container->get('swp_updater.manager');
        $updater->download($resource);

        return array(
            '_status' => 'OK',
            '_items' => $updater->getAvailableUpdates(),
        );
    }

    /**
     * Installs all available updates for given resource (e.g. core, plugin etc).
     * If the updating process fails, it rollback all the changes and throws
     * Exception with status code 500.
     *
     * @ApiDoc(
     *  resource=true,
     *     description="Installs all available updates.",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Returned when fupdate package could not be found.",
     *         422="Returned when given resource doesn't exist.",
     *         500="Returned when instance could not be updated."
     *     }
     * )
     * @Route("/api/updates/install/{resource}", options={"expose"=true})
     * @Method("POST")
     * @Rest\View()
     */
    public function installAction($resource)
    {
        $updater = $this->container->get('swp_updater.manager');
        $updater->applyUpdates($resource);

        return array(
            '_status' => 'OK',
            '_items' => $updater->getAvailableUpdates(),
            'previous_version' => $updater->getCurrentVersion(),
            'current_version' => $updater->getLatestVersion(),
        );
    }

    /**
     * Gets all availbale updates which can be downloaded and installed.
     * Updates can be fetched by diffrent channels: security, default, nightly.
     * * security - security updates,
     * * default - default updates (stable ones),
     * * nightly - not stable updates.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Gets all availbale updates.",
     *     statusCodes={
     *         200="Returned when updates are available.",
     *         404="Returned when updates are not available."
     *     }
     * )
     * @Route("/api/updates/{channel}", options={"expose"=true})
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function getAction($channel = '')
    {
        $updater = $this->container->get('swp_updater.manager');

        return array(
            '_items' => $updater->getAvailableUpdates($channel),
        );
    }

    /**
     * Gets the latest available update package which can be applied
     * to the current application.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Gets the latest available update package."
     * )
     * @Route("/api/updates/latest/", options={"expose"=true})
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function latestAction()
    {
        $updater = $this->container->get('swp_updater.manager');

        return $updater->getLatestUpdate();
    }
}
