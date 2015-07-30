<?php

namespace SWP\UpdaterBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class UpdaterController extends FOSRestController
{
    /**
     * This is the documentation description of your method, it will appear
     * on a specific pane. It will read all the text until the first
     * annotation.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method",
     *  parameters={
     *      {"name"="action", "dataType"="string", "required"=true, "description"="Updater action"}
     *  }
     * )
     * @Route("/api/updates/update", options={"expose"=true})
     * @Method("POST")
     * @Rest\View()
     */
    public function updateAction(Request $request)
    {
        $params = $request->request->all();
        $updater = $this->container->get('swp_updater.manager');
        $updater->updateInstance($params);
    }

    /**
     * This is the documentation description of your method, it will appear
     * on a specific pane. It will read all the text until the first
     * annotation.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     * @Route("/api/updates/check", options={"expose"=true})
     * @Method("GET")
     * @Rest\View()
     */
    public function checkAction()
    {
        $updater = $this->container->get('swp_updater.manager');
        $updater->checkUpdates();

        return array(
            '_items' => $updater->getUpdatesToApply(),
        );
    }

    /**
     * Gets the latest available version.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     * @Route("/api/updates/latest", options={"expose"=true})
     * @Method("GET")
     * @Rest\View()
     */
    public function latestAction()
    {
        $updater = $this->container->get('swp_updater.manager');

        return $updater->getLatestVersion();
    }
}
