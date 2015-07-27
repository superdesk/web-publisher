<?php

namespace SWP\UpdaterBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View as FOSView;
use SWP\UpdaterBundle\Client\ConnectionException;
use GuzzleHttp\Exception\ClientException;

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
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     * @Route("/updater/check", defaults={"_format"="json"}, options={"expose"=true})
     * @Rest\View(serializerGroups={"list"})
     */
    public function getAction()
    {
        $updater = $this->container->get('swp_updater.manager');
        $response = new Response();

        try {
            $updater->checkUpdates();
            if ($updater->isNewVersionAvailable()) {
                dump('New version is available: '.$updater->getLatestVersion());
            } else {
                dump('Your app is up to date');
            }
        } catch (ConnectionException $exception) {
            $response->setContent($exception->getMessage());
        } catch (ClientException $exception) {
            $response->setContent($exception->getMessage());
        }

        return new FOSView\View('test');

        return $response;
    }
}
